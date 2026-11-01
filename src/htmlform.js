/**
 * @file    src/htmlform.js
 * @brief   client-side enhancement for depage-forms: validation, autosave, richtext
 *
 * Standalone ES module — no jQuery required.
 * Uses the native Constraint Validation API instead of the old
 * jQuery-Tools validator.
 *
 * usage:
 *     import DepageForm, { setup, setupForm } from "@depage/htmlform";
 *
 *     // initialize all forms with class "depage-form":
 *     setup();
 *
 *     // or initialize a single form element:
 *     setupForm(formElement);
 *
 * events dispatched on the form element (all bubbling):
 *     - "fail"                              validation failed            detail: { errors }
 *     - "success"                           validation succeeded        detail: { inputs }
 *     - "depageForm.beforeAutosave"         before autosaving (cancelable — call preventDefault() to skip)
 *     - "depageForm.autosaved"              autosave succeeded          detail: { response, result, values }
 *     - "depageForm.autosaveFailed"         autosave failed
 *     - "depageForm.richtextInitialized"    richtext editor is set up   detail: { container, editor }
 *     - "depageForm.showLinkDialog"         the richtext editor wants to edit a link
 *                                           (cancelable)                 detail: { href, callback, editor }
 *
 * the "depageForm.autosaved" event carries the parsed response of the
 * autosave ("result", null when the response is not json) and the values
 * that were sent ("values"), so that a listener can work with them
 * without reading the response body a second time.
 *
 * richtext editor backends:
 *     The richtext editor can use different backends, selected per form
 *     through the "editor" key in data-richtext-options or globally via
 *     DepageForm.defaultEditor:
 *
 *     - "squire" (default) squire-rte based editor
 *     - "tiptap"           TipTap (ProseMirror) based editor with native
 *                          table support
 **/

// {{{ dbid placeholders
/**
 * @brief   counter for the placeholder ids of the nodes the editor creates
 *
 * The editor marks the nodes it creates with a negative id, so that the
 * server can tell them apart from the database ids of the stored nodes.
 * The counter is global (and not per editor), because one autosave
 * request contains the content of all richtext fields of the form and
 * the placeholders have to be unique within the request — the mapping
 * the server returns is unambiguous then, too.
 **/
var dbidPlaceholderCounter = 0;

/**
 * @brief   the next placeholder id for a node the editor created
 *
 * The id is a string, like the ids the editor reads from the markup —
 * the editor does not have to distinguish between the two.
 **/
function nextDbidPlaceholder() {
    dbidPlaceholderCounter -= 1;

    return String(dbidPlaceholderCounter);
}
// }}}

// {{{ throttle()
/**
 * @brief   throttles a function (replaces lodash/throttle)
 *
 * @param   function func function to throttle
 * @param   number wait minimum time between calls in ms
 * @param   object options { leading: true, trailing: true }
 **/
function throttle(func, wait, options = {}) {
    const leading = options.leading !== false;
    const trailing = options.trailing !== false;

    let timeout = null;
    let lastArgs = null;
    let lastThis = null;
    let lastCallTime = 0;

    function invoke() {
        lastCallTime = Date.now();
        timeout = null;
        func.apply(lastThis, lastArgs);
        lastArgs = lastThis = null;
    }

    return function throttled(...args) {
        var now = Date.now();

        if (!lastCallTime && !leading) {
            lastCallTime = now;
        }
        var remaining = wait - (now - lastCallTime);

        lastArgs = args;
        lastThis = this;

        if (remaining <= 0 || remaining > wait) {
            clearTimeout(timeout);
            invoke();
        } else if (!timeout && trailing) {
            timeout = setTimeout(invoke, remaining);
        }
    };
}
// }}}
// {{{ getScrollParent()
/**
 * @brief   returns the closest scrollable parent element (replaces $.fn.scrollParent)
 *
 * @param   element element element to start from
 * @return  element|document
 **/
function getScrollParent(element) {
    var position = getComputedStyle(element).position;
    var excludeStaticParent = position === "absolute";
    var parent = element.parentElement;

    while (parent) {
        if (excludeStaticParent && getComputedStyle(parent).position === "static") {
            parent = parent.parentElement;

            continue;
        }
        var style = getComputedStyle(parent);

        if (/(auto|scroll)/.test(style.overflow + style.overflowY + style.overflowX)) {
            return parent;
        }
        parent = parent.parentElement;
    }

    return element.ownerDocument || document;
}
// }}}
// {{{ splitTag()
/**
 * @brief   splits a tag-string like "h1.classname" into tag, className and attributes
 **/
function splitTag(tag) {
    var result = /([a-zA-Z0-9]*)(\.(.*))?/.exec(tag);

    var attr = [];

    if (typeof result[3] != 'undefined') {
        attr['class'] = result[3];
    }

    return {
        tag: result[1],
        className: result[3],
        attributes: attr
    };
}
// }}}
// {{{ emit()
/**
 * @brief   dispatches a bubbling CustomEvent and returns it
 **/
function emit(target, type, detail = null, cancelable = false) {
    var event = new CustomEvent(type, {
        bubbles: true,
        cancelable: cancelable,
        detail: detail
    });

    target.dispatchEvent(event);

    return event;
}
// }}}
// {{{ toSearchParams()
/**
 * @brief   converts a getFormValues()-object into URLSearchParams
 *
 * Arrays are appended multiple times under the same name, so a
 * field named "example[]" arrives as an array in PHP — the same
 * wire format that jQuery's $.post() produced.
 **/
function toSearchParams(data) {
    var params = new URLSearchParams();

    for (var [key, value] of Object.entries(data)) {
        if (Array.isArray(value)) {
            for (var v of value) {
                params.append(key, v);
            }
        } else {
            params.append(key, value);
        }
    }

    return params;
}
// }}}
// {{{ getElement()
/**
 * @brief   accepts an element, a jQuery object or a selector and returns an element
 **/
function getElement(el) {
    if (typeof el === "string") {
        return document.querySelector(el);
    } else if (el && el.jquery) {
        return el[0];
    }

    return el;
}
// }}}

// {{{ moveRangeBoundariesDownTree()
/**
 * @brief   moves range boundaries down to the deepest text-nodes
 *
 * taken from the squire API that was public in squire v1
 * (Squire.moveRangeBoundariesDownTree) — used for table cell navigation
 **/
function getLength(node) {
    return node.nodeType === Node.TEXT_NODE ? node.length : node.childNodes.length;
}
function isLeaf(node) {
    return node.nodeType === Node.TEXT_NODE || node.nodeName === "BR" || node.nodeName === "IMG";
}
function moveRangeBoundariesDownTree(range) {
    var startContainer = range.startContainer,
        startOffset = range.startOffset,
        endContainer = range.endContainer,
        endOffset = range.endOffset,
        maySkipBR = true,
        child;

    while (startContainer.nodeType !== Node.TEXT_NODE) {
        child = startContainer.childNodes[startOffset];
        if (!child || isLeaf(child)) {
            break;
        }
        startContainer = child;
        startOffset = 0;
    }
    if (endOffset) {
        while (endContainer.nodeType !== Node.TEXT_NODE) {
            child = endContainer.childNodes[endOffset - 1];
            if (!child || isLeaf(child)) {
                if (maySkipBR && child && child.nodeName === 'BR') {
                    endOffset -= 1;
                    maySkipBR = false;
                    continue;
                }
                break;
            }
            endContainer = child;
            endOffset = getLength(endContainer);
        }
    } else {
        while (endContainer.nodeType !== Node.TEXT_NODE) {
            child = endContainer.childNodes[endOffset];
            if (!child || isLeaf(child)) {
                break;
            }
            endContainer = child;
            endOffset = 0;
        }
    }
    range.setStart(startContainer, startOffset);
    range.setEnd(endContainer, endOffset);
}
// }}}

/* {{{ Toolbar */
/**
 * @brief   toolbar for the richtext editor
 **/
class Toolbar {
    // {{{ constructor()
    constructor(container, options, editor) {
        this.container = container;
        this.editor = editor;
        this.options = options;
        this.visible = false;
        this.hideTimeout = null;
        this.mouseX = null;
        this.scrollParent = null;

        this.onDocumentClick = () => {
            this.hide();
        };
        this.onScrollParentScroll = () => {
            this.hide(true);
        };

        this.toolbar = document.createElement("ul");
        this.toolbar.className = "depageEditorToolbar";
        this.toolbar.addEventListener("click", (e) => {
            e.stopPropagation();
        });
        this.container.addEventListener("click", (e) => {
            e.stopPropagation();
        });
        this.container.addEventListener("mousemove", (e) => {
            this.mouseX = e.offsetX;
        });

        this.tagToFunction = {
            'a': {
                add: () => {
                    editor.showLinkDialog("", (value) => {
                        editor.changeFormat(null, { tag: "a" });
                        if (value != "") {
                            editor.changeFormat({ tag: "a", attributes: { href: value } });
                        }
                        editor.focus();
                    });
                },
                remove: () => { editor.changeFormat(null, { tag: "a" }); }
            },
            'b': "inline",
            'i': "inline",
            'u': "inline",
            's': "inline",
            'small': "inline",
            'sub': "inline",
            'sup': "inline",
            'div': "block",
            'p': "block",
            'h1': "block",
            'h2': "block",
            'h3': "block",
            'h4': "block",
            'h5': "block",
            'h6': "block",
            'ul': {
                add: () => { editor.makeUnorderedList(); },
                remove: () => { editor.removeList(); }
            },
            'ol': {
                add: () => { editor.makeOrderedList(); },
                remove: () => { editor.removeList(); }
            }
        };

        var buttonTags = options.allowedTags.slice();
        var hasOtherBlocks = buttonTags.indexOf("ul") !== -1 ||
            buttonTags.indexOf("ol") !== -1 ||
            buttonTags.indexOf("h1") !== -1 ||
            buttonTags.indexOf("h2") !== -1 ||
            buttonTags.indexOf("h3") !== -1 ||
            buttonTags.indexOf("h4") !== -1 ||
            buttonTags.indexOf("h5") !== -1 ||
            buttonTags.indexOf("h6") !== -1;

        var pindex = buttonTags.indexOf("p");
        if (!hasOtherBlocks && pindex !== -1) {
            buttonTags.splice(pindex, 1);
        }

        for (var i = 0; i < buttonTags.length; i++) {
            this.addButton(buttonTags[i]);
        }
    }
    // }}}
    // {{{ addButton()
    addButton(newtag) {
        var result = splitTag(newtag);

        if (typeof this.tagToFunction[result.tag] !== 'undefined') {
            var className = newtag.replace(".", "-");
            var title = result.tag;
            if (title == "small") {
                title = "s";
            }
            var button = document.createElement("li");
            button.className = "format-" + className;
            button.title = newtag;
            button.textContent = title;

            var editor = this.editor;
            var tagToFunction = this.tagToFunction;

            button.addEventListener("click", () => {
                if (tagToFunction[result.tag] == "inline") {
                    if (editor.hasFormat(newtag)) {
                        editor.changeFormat(null, { tag: newtag });
                    } else {
                        editor.changeFormat({ tag: newtag });
                    }
                } else if (tagToFunction[result.tag] == "block") {
                    if (editor.hasFormat(result.tag, result.attributes)) {
                        editor.replaceBlock("p");
                    } else {
                        editor.replaceBlock(newtag);
                    }
                } else {
                    if (editor.hasFormat(newtag)) {
                        tagToFunction[newtag].remove();
                    } else {
                        tagToFunction[newtag].add();
                    }
                }
                editor.focus();
            });

            this.toolbar.appendChild(button);
        }

        return this;
    }
    // }}}
    // {{{ show()
    show() {
        if (this.toolbar.children.length == 0) return;

        document.body.appendChild(this.toolbar);

        this.visible = true;

        for (var i = 0; i < this.options.allowedTags.length; i++) {
            var tag = this.options.allowedTags[i];
            // class names only contain letters, digits and "-"
            var className = tag.replace(".", "-");
            var result = splitTag(tag);
            var button = this.toolbar.querySelector("li.format-" + className);

            if (button) {
                button.classList.toggle("active", this.editor.hasFormat(result.tag, result.attributes));
            }
        }

        document.removeEventListener("click", this.onDocumentClick);
        document.addEventListener("click", this.onDocumentClick);

        this.scrollParent = getScrollParent(this.container);
        this.scrollParent.removeEventListener("scroll", this.onScrollParentScroll);
        this.scrollParent.addEventListener("scroll", this.onScrollParentScroll);

        return this;
    }
    // }}}
    // {{{ hide()
    hide(immediately) {
        var wait = immediately === true ? 0 : 200;

        this.visible = false;

        clearTimeout(this.hideTimeout);
        this.hideTimeout = setTimeout(() => {
            if (!this.visible) {
                this.toolbar.remove();

                document.removeEventListener("click", this.onDocumentClick);
                if (this.scrollParent) {
                    this.scrollParent.removeEventListener("scroll", this.onScrollParentScroll);
                }
            }
        }, wait);

        return this;
    }
    // }}}
    // {{{ setPositionBySelection()
    /**
     * @brief   positions the toolbar centered above the selection
     *
     * Passes 0 as the left-offset: the stylesheet centers the toolbar
     * on its left-position through transform: translateX(-50%). The
     * old jquery code compensated for that implicitly, because
     * $.fn.offset() measures the rendered box (including the
     * transform) — setting style.left directly must not subtract half
     * the toolbar width again.
     **/
    setPositionBySelection() {
        var pos = this.editor.getPosBySelection(this.toolbar.offsetHeight * 1.5, 0);

        this.toolbar.style.top = pos.top + "px";
        this.toolbar.style.left = pos.left + "px";

        return this;
    }
    // }}}
}
/* }}} */

/* {{{ TipTap backend */
// {{{ tiptapProtocolsRegistered
/**
 * @brief   guards the one-time linkifyjs protocol registration
 **/
var tiptapProtocolsRegistered = false;
// }}}
// {{{ findLinkRange()
/**
 * @brief   finds the text-range covered by the link-mark around pos
 *
 * @param   object doc prosemirror document
 * @param   number pos position inside the document
 * @return  object|null { from, to, attributes }
 **/
function findLinkRange(doc, pos) {
    var found = null;

    doc.descendants((node, nodePos) => {
        if (found || !node.isText) return;

        var link = node.marks.find((mark) => mark.type.name == "link");

        if (!link) return;

        var from = nodePos;
        var to = nodePos + node.nodeSize;

        if (pos >= from && pos <= to) {
            found = {
                from: from,
                to: to,
                attributes: link.attrs
            };
        }
    });

    return found;
}
// }}}
// {{{ removeTrailingBreaks()
/**
 * @brief   removes the <br> elements at the end of a block element
 *
 * The old squire editor added the line breaks directly into the
 * markup: the php side adds a <br> to every empty paragraph (see
 * HtmlDom::cleanHTML()) and the editor added a <br> for every soft
 * line break. ProseMirror on the other hand renders the line break
 * of an empty paragraph itself (as .ProseMirror-trailingBreak) and
 * only knows a <br> as a hard break inside a paragraph.
 *
 * So the trailing <br>s have to be removed before the content is
 * handed to tiptap — otherwise an empty paragraph would contain a
 * hard break and the text would be typed after the break.
 *
 * Only trailing breaks are removed: a <br> inside a paragraph is a
 * soft line break and has to stay.
 *
 * @param   string html html markup
 * @return  string html markup without trailing <br> elements
 **/
function removeTrailingBreaks(html) {
    if (!html) return html;

    // use the same parsing context as tiptap does, so the content is
    // not changed in any other way than by removing the breaks
    var body = new DOMParser().parseFromString("<body>" + html + "</body>", "text/html").body;

    var stripBreaks = function (node) {
        while (true) {
            var last = null;

            for (var child = node.lastChild; child; child = child.previousSibling) {
                if (child.nodeType == 1) {
                    // the last element child
                    last = child;
                    break;
                }
                if (child.nodeType == 3 && child.data.trim() == "") {
                    // ignore whitespace between the elements
                    continue;
                }
                // there is text content after the breaks — keep them
                return;
            }

            if (!last || last.nodeName != "BR") return;

            node.removeChild(last);
        }
    };

    var elements = body.querySelectorAll("*");

    for (var i = 0; i < elements.length; i++) {
        stripBreaks(elements[i]);
    }
    stripBreaks(body);

    return body.innerHTML;
}
// }}}
// {{{ dbidKey()
/**
 * @brief   key of a mark, to tell the marks of a document apart
 *
 * @param   object mark      the mark to build the key for
 * @param   bool withoutDbid leave the dbid out of the key
 *
 * @return  string the key of the mark
 **/
function dbidKey(mark, withoutDbid = false) {
    var attrs = Object.assign({}, mark.attrs);

    if (withoutDbid) {
        delete attrs.dbid;
    }

    return mark.type.name + "|" + JSON.stringify(attrs);
}
// }}}
// {{{ collectDbidCarriers()
/**
 * @brief   collects the nodes and the mark ranges of a document that carry a dbid
 *
 * A mark can span several nodes; the server stores every rendered
 * element as a node of its own, so every range of a mark needs a dbid of
 * its own.
 *
 * @param   object doc the prosemirror document to walk
 *
 * @return  array the carriers (nodes and mark ranges) in document order
 **/
function collectDbidCarriers(doc) {
    var carriers = [];
    var ranges = [];
    var openRanges = new Map();

    doc.descendants((node, pos) => {
        if (!node.isText) {
            carriers.push({
                kind: "node",
                pos: pos,
                node: node,
                dbid: node.attrs.dbid,
            });
        }

        var to = pos + node.nodeSize;

        node.marks.forEach((mark) => {
            var key = dbidKey(mark);
            var current = openRanges.get(key);

            if (current && current.to === pos) {
                // the mark continues in this node
                current.to = to;
            } else {
                current = { key: key, from: pos, to: to, mark: mark };
                ranges.push(current);
                openRanges.set(key, current);
            }
        });

        return true;
    });

    ranges.forEach((range) => {
        carriers.push({
            kind: "mark",
            from: range.from,
            to: range.to,
            mark: range.mark,
            dbid: range.mark.attrs.dbid,
        });
    });

    carriers.sort((a, b) => {
        var aPos = a.kind == "node" ? a.pos : a.from;
        var bPos = b.kind == "node" ? b.pos : b.from;

        return aPos - bPos;
    });

    return carriers;
}
// }}}
// {{{ setCarrierDbid()
/**
 * @brief   sets the dbid of a node or of a mark range in a transaction
 *
 * @param   object tr      the transaction to change
 * @param   object carrier the carrier to change
 * @param   string dbid    the dbid to set
 **/
function setCarrierDbid(tr, carrier, dbid) {
    if (carrier.kind == "node") {
        tr.setNodeMarkup(
            carrier.pos,
            undefined,
            Object.assign({}, carrier.node.attrs, { dbid: dbid })
        );
    } else {
        // a mark can not be changed in place, it has to be removed and
        // added again with the new attributes
        tr.removeMark(carrier.from, carrier.to, carrier.mark);
        tr.addMark(
            carrier.from,
            carrier.to,
            carrier.mark.type.create(Object.assign({}, carrier.mark.attrs, { dbid: dbid }))
        );
    }
}
// }}}
// {{{ assignDbidPlaceholders()
/**
 * @brief   gives the nodes and marks without a usable dbid a placeholder id
 *
 * The nodes the editor creates (and the nodes of a pasted or duplicated
 * block, which carry the id of the block they were copied from) get a
 * negative placeholder id. The server stores them with a new database id
 * and reports the mapping, so that the editor can replace the
 * placeholders with the database ids afterwards.
 *
 * @param   object state the prosemirror state to work on
 *
 * @return  object the transaction that assigns the placeholders, null if
 *          there is nothing to assign
 **/
function assignDbidPlaceholders(state) {
    var carriers = collectDbidCarriers(state.doc);
    var used = new Set();
    var changes = [];

    for (var i = 0; i < carriers.length; i++) {
        var carrier = carriers[i];
        var dbid = carrier.dbid;

        if (carrier.kind == "node" && !("dbid" in carrier.node.attrs)) {
            // the node type does not know the attribute
            continue;
        }
        if (carrier.kind == "mark" && !("dbid" in carrier.mark.attrs)) {
            continue;
        }

        if (dbid === null || dbid === undefined || dbid === "" || used.has(String(dbid))) {
            changes.push(carrier);
        } else {
            used.add(String(dbid));
        }
    }

    if (changes.length == 0) {
        return null;
    }

    var tr = state.tr;

    for (var i = 0; i < changes.length; i++) {
        setCarrierDbid(tr, changes[i], nextDbidPlaceholder());
    }

    return tr;
}
// }}}
// {{{ applyDbidIds()
/**
 * @brief   replaces the placeholder ids of the document with the database ids
 *
 * The server stores the nodes the editor created with new database ids
 * and reports the mapping from the placeholders to them; the editor
 * replaces the placeholders, so that the next save can match the nodes
 * by their database id.
 *
 * @param   object editor the tiptap editor
 * @param   object ids    mapping from the placeholder id to the database id
 *
 * @return  bool whether the document was changed
 **/
function applyDbidIds(editor, ids) {
    var map = new Map();

    Object.keys(ids || {}).forEach((placeholder) => {
        map.set(String(placeholder), String(ids[placeholder]));
    });

    if (map.size == 0) {
        return false;
    }

    var carriers = collectDbidCarriers(editor.state.doc);
    var changes = [];

    for (var i = 0; i < carriers.length; i++) {
        var carrier = carriers[i];

        if (carrier.dbid === null || carrier.dbid === undefined || carrier.dbid === "") {
            continue;
        }

        if (map.has(String(carrier.dbid))) {
            changes.push(carrier);
        }
    }

    if (changes.length == 0) {
        return false;
    }

    var tr = editor.state.tr;

    for (var i = 0; i < changes.length; i++) {
        setCarrierDbid(tr, changes[i], map.get(String(changes[i].dbid)));
    }

    editor.view.dispatch(tr);

    return true;
}
// }}}
// {{{ buildTipTapExtensions()
/**
 * @brief   builds the tiptap extension list from the allowedTags option
 *
 * With tiptap the allowedTags list is not a filter but the schema:
 * only what is registered here can exist in the document. Tags are
 * mapped onto the corresponding extensions, "tag.class" entries keep
 * their class through generic class-attributes.
 *
 * @param   array allowedTags list of tags like ["p", "a", "h1.teaser"]
 * @param   object imports the dynamically imported tiptap modules
 * @return  array extensions
 **/
function buildTipTapExtensions(allowedTags, imports) {
    var core = imports.core;
    var Paragraph = imports.Paragraph;
    var Heading = imports.Heading;
    var StarterKit = imports.StarterKit;
    var Link = imports.Link;
    var tableModule = imports.table;
    var Subscript = imports.Subscript;
    var Superscript = imports.Superscript;

    var tags = new Set();

    for (var i = 0; i < (allowedTags || []).length; i++) {
        tags.add(splitTag(allowedTags[i]).tag);
    }

    var headingLevels = [];
    for (var level = 1; level <= 6; level++) {
        if (tags.has("h" + level)) {
            headingLevels.push(level);
        }
    }

    var classAttribute = {
        default: null,
        parseHTML: (element) => element.getAttribute("class"),
        renderHTML: (attributes) => attributes.class ? { class: attributes.class } : {},
    };

    // depage stores a database id in every element of the richtext
    // content — as data-dbid in the html; the squire backend kept
    // them through dompurify's allowed attributes. xmldb's
    // saveNodePatch() needs those ids to only save the parts of the
    // tree that actually changed (without them it falls back to
    // saving the whole subtree), so they must survive the schema
    // roundtrip on every node and mark
    var dbidAttribute = {
        default: null,
        parseHTML: (element) => element.getAttribute("data-dbid"),
        renderHTML: (attributes) => attributes.dbid ? { "data-dbid": attributes.dbid } : {},
        // a block created by splitting another one is a new node and
        // must get a new id when saving — squire behaved the same
        // way, because it created the new block without copying the
        // attributes of the old one
        keepOnSplit: false,
    };

    var extensions = [
        core.Extension.create({
            name: "depageDbid",

            // adds the dbid attribute to all node and mark types at
            // once, so it does not have to be repeated for every
            // extension
            addGlobalAttributes() {
                return [{
                    types: "*",
                    attributes: {
                        dbid: dbidAttribute,
                    },
                }];
            },

            // the nodes the editor creates get a placeholder id, so that
            // the server can tell them apart from the stored nodes and
            // report the database ids they were stored with
            addProseMirrorPlugins() {
                return [
                    new imports.Plugin({
                        appendTransaction: (transactions, oldState, newState) => {
                            if (!transactions.some((tr) => tr.docChanged)) {
                                return null;
                            }

                            return assignDbidPlaceholders(newState);
                        },
                    }),
                ];
            },
        }),
        StarterKit.configure({
            // replaced by the extended versions below, so that class
            // attributes survive the roundtrip
            paragraph: false,
            heading: false,
            // the link mark is configured separately below
            link: false,
            bold: tags.has("b") || tags.has("strong"),
            italic: tags.has("i") || tags.has("em"),
            strike: tags.has("s"),
            underline: tags.has("u"),
            bulletList: tags.has("ul"),
            orderedList: tags.has("ol"),
            listItem: tags.has("ul") || tags.has("ol"),
            hardBreak: tags.has("br"),
            blockquote: false,
            code: false,
            codeBlock: false,
            horizontalRule: false,
        }),
        Paragraph.extend({
            addAttributes() {
                return Object.assign({}, this.parent?.(), { class: classAttribute });
            },
        }),
    ];

    if (headingLevels.length > 0) {
        extensions.push(Heading.extend({
            addAttributes() {
                return Object.assign({}, this.parent?.(), { class: classAttribute });
            },
        }).configure({
            levels: headingLevels,
        }));
    }

    if (tags.has("small")) {
        extensions.push(core.Mark.create({
            name: "small",

            parseHTML() {
                return [{ tag: "small" }];
            },

            renderHTML() {
                return ["small", 0];
            },
        }));
    }

    if (tags.has("sub")) {
        extensions.push(Subscript);
    }
    if (tags.has("sup")) {
        extensions.push(Superscript);
    }

    if (tags.has("a")) {
        extensions.push(Link.extend({
            addAttributes() {
                // dbid (data-dbid) comes from the global depageDbid
                // extension
                return Object.assign({}, this.parent?.(), {
                    class: classAttribute,
                });
            },
        }).configure({
            // keep the depage dialog in charge of opening links
            openOnClick: false,
            autolink: true,
            linkOnPaste: true,
            // the depage-internal schemes are registered once with
            // linkifyjs in setupTipTapEditor(); not passing them here
            // keeps the extension from re-registering them for every
            // editor instance (which would warn once linkify is
            // initialized)
            protocols: [],
            // …but parse-time validation must still accept them: the
            // default check only allows the standard schemes
            isAllowedUri: (url, ctx) => !!imports.linkIsAllowedUri(url, ["pageref", "libref", "footnote"]),
        }));
    }

    if (tags.has("table")) {
        var Table = tableModule.Table;
        var TableRow = tableModule.TableRow;
        var TableCell = tableModule.TableCell;
        var TableHeader = tableModule.TableHeader;
        var TextSelection = imports.TextSelection;

        extensions.push(Table.extend({
            // render plain <table><tbody>…</tbody></table> markup without
            // the colgroups and inline widths that the default adds, so
            // the saved html stays compatible with depage's stylesheets
            // and xslt templates
            renderHTML({ HTMLAttributes }) {
                return [
                    "table",
                    core.mergeAttributes(this.options.HTMLAttributes, HTMLAttributes),
                    ["tbody", 0],
                ];
            },
        }).configure({
            // no TableView / tableWrapper div — keep depage's DOM structure
            View: null,
            resizable: false,
        }));
        extensions.push(TableRow);
        extensions.push(TableCell);
        extensions.push(TableHeader);

        // {{{ Tab navigation between cells
        // selects the content of the next/previous cell, so that typing
        // replaces it — the same behaviour as the squire implementation
        extensions.push(core.Extension.create({
            name: "depageTabCellNavigation",

            addKeyboardShortcuts() {
                var selectCell = (forward) => {
                    var editor = this.editor;
                    var state = editor.state;
                    var $from = state.selection.$from;

                    var cells = [];
                    var current = -1;

                    state.doc.descendants((node, pos) => {
                        if (node.type.name == "tableCell" || node.type.name == "tableHeader") {
                            if (pos < $from.pos && $from.pos < pos + node.nodeSize) {
                                current = cells.length;
                            }

                            cells.push({
                                pos: pos,
                                size: node.nodeSize
                            });
                        }
                    });

                    if (current == -1) return false;

                    var index = current + (forward ? 1 : -1);

                    if (index < 0 || index >= cells.length) return false;

                    var cell = cells[index];
                    var selection = TextSelection.create(state.doc, cell.pos + 1, cell.pos + cell.size - 1);

                    editor.view.dispatch(state.tr.setSelection(selection).scrollIntoView());

                    return true;
                };

                return {
                    Tab: () => selectCell(true),
                    "Shift-Tab": () => selectCell(false),
                };
            },
        }));
        // }}}
    }

    return extensions;
}
// }}}
// {{{ TipTapFacade
/**
 * @brief   exposes the editor API used by the Toolbar on a tiptap editor
 *
 * Implements the same surface as the patched Squire instance, so the
 * Toolbar, the link dialog and the table row buttons work with both
 * backends unchanged.
 **/
class TipTapFacade {
    // {{{ constructor()
    constructor(editor, container) {
        this.editor = editor;
        this.container = container;
        this._root = editor.view.dom;
    }
    // }}}
    // {{{ basics
    focus() {
        this.editor.commands.focus();

        return this;
    }

    blur() {
        this.editor.commands.blur();

        return this;
    }

    getHTML() {
        return this.editor.getHTML();
    }

    setHTML(html) {
        // remove the stray line breaks the php side adds to empty
        // paragraphs — otherwise they would end up as hard breaks in
        // the document
        this.editor.commands.setContent(removeTrailingBreaks(html || ""), false);

        return this;
    }

    setEditable(editable) {
        this.editor.setEditable(editable);

        return this;
    }
    // }}}
    // {{{ inlineMarkFor()
    /**
     * @brief   maps html tags to tiptap mark names
     **/
    inlineMarkFor(tag) {
        return {
            b: "bold",
            strong: "bold",
            i: "italic",
            em: "italic",
            u: "underline",
            s: "strike",
            small: "small",
            sub: "subscript",
            sup: "superscript",
        }[tag];
    }
    // }}}
    // {{{ hasFormat()
    hasFormat(tag, attributes) {
        var result = splitTag(tag);

        tag = result.tag;
        attributes = attributes || result.attributes;

        var mark = this.inlineMarkFor(tag);

        if (mark) {
            return this.editor.isActive(mark);
        }
        if (tag == "a") {
            return this.editor.isActive("link");
        }
        if (tag == "ul") {
            return this.editor.isActive("bulletList");
        }
        if (tag == "ol") {
            return this.editor.isActive("orderedList");
        }
        if (/^h[1-6]$/.test(tag)) {
            return this.editor.isActive("heading", Object.assign({ level: parseInt(tag[1]) }, attributes));
        }
        if (tag == "p") {
            return this.editor.isActive("paragraph", attributes);
        }

        return false;
    }
    // }}}
    // {{{ changeFormat()
    /**
     * @brief   adds and/or removes a format — same signature as squire
     *
     * @param   object|null add { tag, attributes } format to add
     * @param   object|null remove { tag } format to remove
     **/
    changeFormat(add, remove) {
        if (remove) {
            this.removeFormat(remove);
        }
        if (add) {
            this.applyFormat(add);
        }

        return this;
    }
    // }}}
    // {{{ applyFormat()
    applyFormat(spec) {
        var result = splitTag(spec.tag);
        var tag = result.tag;
        var attributes = Object.assign({}, result.attributes, spec.attributes);

        var mark = this.inlineMarkFor(tag);

        if (mark) {
            this.editor.chain().focus().setMark(mark, attributes).run();
        } else if (tag == "a") {
            if (attributes.href && this.editor.commands.setLink) {
                this.editor.chain().focus().extendMarkRange("link").setLink(attributes).run();
            }
        } else {
            this.replaceBlock(spec.tag);
        }

        return this;
    }
    // }}}
    // {{{ removeFormat()
    removeFormat(spec) {
        var tag = splitTag(spec.tag).tag;
        var mark = this.inlineMarkFor(tag);

        if (mark) {
            this.editor.chain().focus().unsetMark(mark).run();
        } else if (tag == "a") {
            if (this.editor.commands.unsetLink) {
                this.editor.chain().focus().extendMarkRange("link").unsetLink().run();
            }
        } else if (tag == "ul" || tag == "ol") {
            this.removeList();
        }

        return this;
    }
    // }}}
    // {{{ replaceBlock()
    replaceBlock(tag) {
        var result = splitTag(tag);
        var attributes = {};

        if (result.className !== undefined) {
            attributes.class = result.className;
        }

        if (/^h[1-6]$/.test(result.tag)) {
            this.editor.chain().focus().setNode("heading", Object.assign({ level: parseInt(result.tag[1]) }, attributes)).run();
        } else {
            this.editor.chain().focus().setNode("paragraph", attributes).run();
        }

        return this;
    }
    // }}}
    // {{{ lists
    makeUnorderedList() {
        this.editor.chain().focus().toggleBulletList().run();

        return this;
    }

    makeOrderedList() {
        this.editor.chain().focus().toggleOrderedList().run();

        return this;
    }

    removeList() {
        this.editor.chain().focus().liftListItem("listItem").run();

        return this;
    }
    // }}}
    // {{{ getPosBySelection()
    /**
     * @brief   returns the position of the current editor selection
     *
     * Used to place floating UI (toolbar, link dialog) near the
     * selection. Measures the selection stored in the prosemirror
     * state through view.coordsAtPos() — reading the live window
     * selection is not reliable here: as soon as a floating toolbar
     * button is pressed, the editor and with it the dom selection lose
     * focus, while the stored prosemirror selection survives. That
     * stored selection is what the toolbar and the link dialog must be
     * positioned against.
     *
     * @param   number offsetTop offset to move the position up
     * @param   number offsetLeft offset to move the position left
     * @return  object { top, left } in page coordinates
     **/
    getPosBySelection(offsetTop, offsetLeft) {
        offsetTop = offsetTop || 0;
        offsetLeft = offsetLeft || 0;

        var view = this.editor.view;
        var selection = this.editor.state.selection;

        var from = view.coordsAtPos(selection.from);
        var to = view.coordsAtPos(selection.to);

        var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        var scrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;

        // @todo check if mouse position is inside rect and position the toolbar next to mouse
        // @todo check that toolbar stays in visible area of window
        // @todo change position only if necessary (if selection has changed)
        // @todo adjust toolbar position on iOS because of native popup

        var left = Math.min(from.left, to.left);
        var right = Math.max(from.right, to.right);
        var top = Math.min(from.top, to.top) + scrollTop - offsetTop;

        left = left + (right - left) * 0.5 + scrollLeft - offsetLeft;

        if (top < 60) {
            top = 60;
        }

        return {
            top: top,
            left: left
        };
    }
    // }}}
    // {{{ showLinkDialog()
    showLinkDialog(href, callback) {
        emit(this._root, "depageForm.showLinkDialog", {
            href: href,
            callback: callback,
            editor: this
        }, true);

        return this;
    }
    // }}}
    // {{{ handleTableButtonClick()
    handleTableButtonClick(e, cell) {
        cell = cell || e.currentTarget;

        // the "+" and "-" buttons are rendered through css inside the
        // right padding-area of the last cell — same geometry check as
        // the squire implementation
        var cellStyle = getComputedStyle(cell);
        var contentWidth = cell.clientWidth
            - parseFloat(cellStyle.paddingLeft)
            - parseFloat(cellStyle.paddingRight);

        if (e.offsetX < contentWidth) return;
        if (!this.editor.commands.addRowAfter) return;

        var row = cell.parentElement;
        var pos = this.editor.view.posAtDOM(cell, 0);

        if (e.offsetY < 20) {
            this.editor.chain().focus().setTextSelection(pos).addRowAfter().run();
        } else if (e.offsetY < 40) {
            // keep at least one row, like the squire implementation
            if (row.parentElement && row.parentElement.children.length > 1) {
                this.editor.chain().focus().setTextSelection(pos).deleteRow().run();
            }
        }

        return false;
    }
    // }}}
    // {{{ destroy()
    destroy() {
        this.editor.destroy();
    }
    // }}}
}
/* }}} */

/* {{{ DepageForm */
export class DepageForm {
    // {{{ constructor()
    /**
     * @brief   enhances a depage-form form-element
     *
     * @param   element form form element (or selector / jQuery object)
     **/
    constructor(form) {
        this.form = getElement(form);
        this.check = this.form.getAttribute("data-jsvalidation");
        this.autosaveEnabled = this.form.getAttribute("data-jsautosave") == "true";

        // autosave state
        this.state = {
            hasChanged: false,
            saving: false,
            submitting: false,
            lastsave: Date.now(),
            lastSaved: null,
            saveTimer: null,
            successTimer: null,
        };
        this.saveInterval = 300;
        this.submitTimeout = null;

        // validation state
        this.skipValidation = false;
    }
    // }}}

    /* {{{ form setup */
    // {{{ async init()
    /**
     * @brief   sets up all form enhancements
     *
     * The returned promise is also available as instance.ready.
     **/
    async init() {
        if (this.initializing) return this;
        this.initializing = true;

        this.addErrorHandlers();
        this.addTextareaHandlers();
        this.addFocusClassHandlers();
        this.addUrlHandlers();

        // focus first input with autofocus
        var autofocus = this.form.querySelector("input[autofocus]");
        if (autofocus) {
            autofocus.focus();
        }

        var rtfInputs = this.form.querySelectorAll(".input-richtext");
        for (var i = 0; i < rtfInputs.length; i++) {
            try {
                await this.setupRichtextEditor(rtfInputs[i]);
            } catch (exception) {
                console.warn("depage-forms: could not initialize richtext editor", exception);
            }
        }

        try {
            this.setupSelects();
        } catch (exception) {
            console.warn("depage-forms: could not initialize selects", exception);
        }

        this.attachValidation();

        if (this.autosaveEnabled) {
            this.setupAutosave();
        }

        return this;
    }
    // }}}
    // {{{ addErrorHandlers()
    /**
     * @brief   hides error messages when the input gets focus and
     *          focuses the input when the error message is clicked
     **/
    addErrorHandlers() {
        var form = this.form;

        // hide errormessages on focus (focus does not bubble -> focusin)
        form.addEventListener("focusin", (e) => {
            if (!e.target.matches("input, select, textarea")) return;

            var errorParagraph = e.target.closest(".error");
            var errorMessage = errorParagraph && errorParagraph.querySelector(".errormessage");

            if (errorMessage) {
                errorMessage.style.display = "none";
            }
        });

        // focus input on click on error message
        form.addEventListener("click", (e) => {
            var errorMessage = e.target.closest(".errormessage");

            if (!errorMessage || !form.contains(errorMessage)) return;

            var errorParagraph = errorMessage.closest(".error");
            var input = errorParagraph && errorParagraph.querySelector("input, select, textarea");

            if (input) {
                input.focus();

                try {
                    // try to open select on click
                    input.dispatchEvent(new MouseEvent("mousedown", { bubbles: true, cancelable: true }));
                } catch (exception) {
                }
            }
        });
    }
    // }}}
    // {{{ addTextareaHandlers()
    /**
     * @brief   adds autogrow-behavior to textareas
     **/
    addTextareaHandlers() {
        this.form.querySelectorAll(".input-textarea").forEach((container) => {
            var options = {};

            try {
                options = JSON.parse(container.getAttribute("data-textarea-options")) || {};
            } catch (exception) {
            }

            var textarea = container.querySelector("textarea");

            if (!textarea || !options.autogrow) return;

            var textareaSize = document.createElement("textarea");
            textareaSize.className = "textarea-size";
            textareaSize.style.position = "absolute";
            textareaSize.style.left = "-10000px";
            textareaSize.style.height = "auto";
            textarea.parentElement.appendChild(textareaSize);

            var autogrow = function() {
                textareaSize.value = textarea.value;

                // set new height
                var newHeight = textareaSize.scrollHeight + 30;
                textarea.style.height = newHeight + "px";
            };

            textarea.autogrow = autogrow;

            for (var event of ["keyup", "keydown", "scroll"]) {
                textarea.addEventListener(event, autogrow);
            }

            autogrow();
        });
    }
    // }}}
    // {{{ addFocusClassHandlers()
    /**
     * @brief   adds a focus-class to the fieldset of the focused input
     *
     * For richtext editors the focus target is the editable element:
     * the .textarea-content div itself for the squire backend, a child
     * of it (the ProseMirror root) for the tiptap backend — so richtext
     * membership is checked with closest() instead of matches().
     **/
    addFocusClassHandlers() {
        var selector = "input, textarea, select";

        var getFieldset = (target) => {
            if (target.matches(selector)) {
                return target.closest("fieldset");
            }

            var content = target.closest(".input-richtext .textarea-content");

            return content ? content.closest("fieldset") : null;
        };

        this.form.addEventListener("focusin", (e) => {
            var fieldset = getFieldset(e.target);

            if (fieldset) fieldset.classList.add("focus");
        });
        this.form.addEventListener("focusout", (e) => {
            var fieldset = getFieldset(e.target);

            if (fieldset) fieldset.classList.remove("focus");
        });
    }
    // }}}
    // {{{ addUrlHandlers()
    /**
     * @brief   adds missing "http://" to url inputs
     **/
    addUrlHandlers() {
        this.form.addEventListener("change", (e) => {
            var input = e.target;

            if (input.matches && input.matches("input[type='url']") &&
                input.value !== "" &&
                !input.value.match(/[a-z][\w\-]+:\/\/.*/)
            ) {
                input.value = "http://" + input.value;
            }
        });
    }
    // }}}
    /* }}} */

    /* {{{ validation */
    // {{{ getInputs()
    /**
     * @brief   returns the inputs of the form that take part in validation
     *
     * Skips buttons and disabled elements and filters duplicate elements
     * by name (radio/checkbox-groups are validated once per name) —
     * same behavior as the old jQuery-Tools validator.
     *
     * @return  array input elements
     **/
    getInputs() {
        var inputs = [];
        var names = {};

        for (var el of this.form.elements) {
            if (el.tagName !== "INPUT" && el.tagName !== "SELECT" && el.tagName !== "TEXTAREA") continue;
            if (el.disabled) continue;

            if (el.name && names[el.name]) continue;
            names[el.name] = true;

            inputs.push(el);
        }

        return inputs;
    }
    // }}}
    // {{{ getErrorMessageFor()
    /**
     * @brief   returns the error message for an invalid input
     *
     * Prefers the message rendered by depage-forms (data-errormessage
     * attribute of the surrounding paragraph) and falls back to the
     * browser validation message.
     **/
    getErrorMessageFor(input) {
        var paragraph = input.closest("p");
        var message = paragraph && paragraph.getAttribute("data-errormessage");

        return message || input.validationMessage;
    }
    // }}}
    // {{{ showErrors()
    /**
     * @brief   shows error messages for invalid inputs (the old "depageEffect")
     *
     * Adds the error-class to the paragraph of the input and appends an
     * errormessage-span with the text of the data-errormessage attribute.
     **/
    showErrors(errors) {
        for (var { input } of errors) {
            var errorParagraph = input.closest("p");

            if (!errorParagraph) continue;

            var errorMessage = errorParagraph.querySelector(".errormessage");

            if (!errorMessage) {
                errorMessage = document.createElement("span");
                errorMessage.className = "errormessage";
                errorMessage.textContent = this.getErrorMessageFor(input);

                errorParagraph.appendChild(errorMessage);
            } else {
                errorMessage.style.display = "";
            }

            errorParagraph.classList.add("error");
        }
    }
    // }}}
    // {{{ hideErrors()
    /**
     * @brief   removes error messages of inputs that turned valid
     **/
    hideErrors(inputs) {
        for (var input of inputs) {
            var inputParagraph = input.closest("p");

            if (!inputParagraph) continue;

            inputParagraph.classList.remove("error");
            inputParagraph.querySelectorAll(".errormessage").forEach((el) => {
                el.remove();
            });
        }
    }
    // }}}
    // {{{ checkValidity()
    /**
     * @brief   validates the given inputs or all inputs of the form
     *
     * @param   array els input elements to validate (optional)
     * @return  bool true when all given inputs are valid
     **/
    checkValidity(els) {
        els = els || this.getInputs();

        if (!els.length) return true;

        var errors = [];

        for (var el of els) {
            if (!el.checkValidity()) {
                errors.push({ input: el, messages: [this.getErrorMessageFor(el)] });
            }
        }

        if (errors.length) {
            this.showErrors(errors);
            emit(this.form, "fail", { errors: errors });

            return false;
        }

        this.hideErrors(els);
        emit(this.form, "success", { inputs: els });

        return true;
    }
    // }}}
    // {{{ getGroupOf()
    /**
     * @brief   returns all inputs of a radio/checkbox-group by name
     **/
    getGroupOf(input) {
        if (!input.name) return [input];

        return Array.from(this.form.querySelectorAll(
            "input[name=\"" + CSS.escape(input.name) + "\"], select[name=\"" + CSS.escape(input.name) + "\"]"
        ));
    }
    // }}}
    // {{{ attachValidation()
    /**
     * @brief   attaches js-validation to the form
     *
     * Validation mode is read from the data-jsvalidation attribute:
     *     - "blur"   validate each input when it loses focus
     *     - "change" validate each input when it changes
     *     - "submit" only validate when the form is submitted
     *
     * The browser default validation UI is disabled (novalidate), the
     * errors are displayed by showErrors() instead.
     **/
    attachValidation() {
        var form = this.form;
        var check = this.check;

        if (check != 'blur' && check != 'change' && check != 'submit') return;

        // disable browser default validation ui
        form.setAttribute("novalidate", "novalidate");

        // validate on blur or change
        if (check == 'blur' || check == 'change') {
            var inputEvent = check == "blur" ? "focusout" : "change";

            form.addEventListener(inputEvent, (e) => {
                var input = e.target;

                if (!input.matches("input, select, textarea") || input.disabled) return;

                // validate radio/checkbox-groups by name
                if (input.type == "radio" || input.type == "checkbox") {
                    this.checkValidity(this.getGroupOf(input));
                } else {
                    this.checkValidity([input]);
                }
            });
        }

        // cancel/back buttons submit without validation
        form.querySelectorAll("p.cancel input[type='submit'], p.back input[type='submit']").forEach((button) => {
            button.addEventListener("click", () => {
                this.skipValidation = true;
            });
        });

        // validate on submit
        form.addEventListener("submit", (e) => {
            if (this.skipValidation) return;

            if (!this.checkValidity()) {
                e.preventDefault();
            }
        });

        // mark submit-button as erroneous while there are error messages
        form.addEventListener("fail", () => {
            form.querySelectorAll(".submit").forEach((el) => {
                el.classList.add("error");
            });
        });
        form.addEventListener("success", () => {
            // count error messages, because success is called for every
            // successfully validated input
            var errNum = form.querySelectorAll(".errormessage").length;

            form.querySelectorAll(".submit").forEach((el) => {
                el.classList.toggle("error", errNum > 0);
            });
        });
    }
    // }}}
    /* }}} */

    /* {{{ autosave */
    // {{{ getFormValues()
    /**
     * @brief   collects the current values of all form inputs
     *
     * Same semantics as the old implementation: inputs inside a
     * .cancel-paragraph are skipped, checkboxes inside .input-boolean
     * paragraphs are single values, other checkboxes are collected
     * as arrays.
     *
     * @return  object form values + formAutosave=true
     **/
    getFormValues() {
        var data = {};

        this.form.querySelectorAll("input, select, textarea").forEach((el) => {
            if (el.parentElement && el.parentElement.classList.contains("cancel")) return;
            if (el.name == "") return;

            var type = el.type;

            if (type == "radio") {
                if (el.checked) {
                    data[el.name] = el.value;
                }
            } else if (type == "checkbox") {
                var paragraph = el.closest("p");

                if (paragraph && paragraph.classList.contains("input-boolean")) {
                    if (el.checked) {
                        data[el.name] = el.value;
                    }
                } else {
                    if (el.checked) {
                        data[el.name] = data[el.name] || [];
                        data[el.name].push(el.value);
                    }
                }
            } else if (el.tagName === "SELECT" && el.multiple) {
                data[el.name] = Array.from(el.selectedOptions).map((option) => option.value);
            } else {
                data[el.name] = el.value;
            }
        });

        data.formAutosave = "true";

        return data;
    }
    // }}}
    // {{{ setupAutosave()
    /**
     * @brief   sets up autosaving of the form
     **/
    setupAutosave() {
        var form = this.form;

        form.classList.add("autosave");

        this.state.lastSaved = this.getFormValues();

        // normal inputs
        form.addEventListener("change", (e) => {
            if (e.target.matches("input, select, textarea")) {
                this.changed(e.target.type != "hidden");
            }
        });
        // only for inputs and textareas
        form.addEventListener("keyup", (e) => {
            if (!e.target.matches("input, textarea")) return;
            // ignore tom-select's search input
            if (e.target.closest(".ts-wrapper")) return;

            this.changed();
        });

        form.addEventListener("submit", (e) => {
            clearTimeout(this.submitTimeout);

            if (this.state.saving) {
                // form is still saving -> do not submit
                e.preventDefault();

                this.submitTimeout = setTimeout(() => {
                    form.requestSubmit();
                }, 100);

                return;
            }

            // set submitting so the form is not autosaved anymore
            this.state.submitting = true;
        });
    }
    // }}}
    // {{{ changed()
    /**
     * @brief   called when the form content changed — starts autosaving
     *
     * @param   bool saveImmediately save without waiting for the save-interval
     **/
    changed(saveImmediately) {
        this.state.hasChanged = true;

        clearTimeout(this.state.saveTimer);

        if (!this.state.saving &&
            (saveImmediately || Date.now() - this.state.lastsave > this.saveInterval)
        ) {
            this.autosave();

            return;
        }

        this.state.saveTimer = setTimeout(() => {
            this.changed();
        }, this.saveInterval);
    }
    // }}}
    // {{{ autosave()
    /**
     * @brief   saves the form data via POST against the form action
     *
     * On failure the response is parsed as HTML: if it contains a new
     * CSRF-token (e.g. because the session expired) the token is updated
     * and the save is retried once.
     **/
    async autosave() {
        var form = this.form;

        // trigger event before autosaving — cancelable
        var beforeEvent = emit(form, "depageForm.beforeAutosave", null, true);
        if (beforeEvent.defaultPrevented) return;

        var data = this.getFormValues();

        if (JSON.stringify(data) === JSON.stringify(this.state.lastSaved)) {
            // data is the same -> skip save
            return;
        }

        form.classList.remove("autosave-success");
        form.classList.add("autosaving");
        this.state.saving = true;
        this.state.hasChanged = false;

        var submitButtons = form.querySelectorAll("input[type='submit'], button[type='submit']");

        submitButtons.forEach((button) => {
            button.setAttribute("data-was-disabled", button.disabled ? "true" : "false");
            button.disabled = true;
        });

        clearTimeout(this.state.successTimer);

        try {
            var response = await fetch(form.action, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
                body: toSearchParams(data),
            });

            if (!response.ok) {
                throw response;
            }

            this.state.lastsave = Date.now();
            this.state.lastSaved = data;

            form.classList.add("autosave-success");
            form.classList.remove("autosave-failed");

            // the parsed response is passed on, so that a listener can
            // work with it (the mapping of the ids of the new nodes, for
            // example) without reading the body of the response again
            var result = null;
            try {
                result = await response.clone().json();
            } catch (exception) {
                result = null;
            }

            emit(form, "depageForm.autosaved", { response: response, result: result, values: data });

            this.state.successTimer = setTimeout(() => {
                form.classList.remove("autosave-success");
            }, 1000);
        } catch (error) {
            if (this.state.submitting) {
                // form is submitted through default action
                return;
            }

            var retried = false;

            try {
                var text = (error && typeof error.text == "function") ? await error.text() : "";
                var doc = new DOMParser().parseFromString(text, "text/html");
                var i1 = form.querySelector(":scope > input[name='formCsrfToken']");
                var i2 = doc.querySelector("#" + CSS.escape(form.id) + " > input[name='formCsrfToken']");

                if (i1 && i2 && i1.value != i2.value) {
                    // set new csrf token
                    i1.value = i2.value;

                    this.autosave();

                    retried = true;
                }
            } catch (exception) {
            }

            if (!retried) {
                form.classList.add("autosave-failed");
                form.classList.remove("autosave-success");
                emit(form, "depageForm.autosaveFailed");
            }
        } finally {
            this.state.saving = false;
            form.classList.remove("autosaving");

            submitButtons.forEach((button) => {
                button.disabled = button.getAttribute("data-was-disabled") == "true";
            });
        }
    }
    // }}}
    /* }}} */

    /* {{{ richtext */
    // {{{ setupRichtextEditor()
    /**
     * @brief   replaces a textarea with a richtext editor
     *
     * Builds the shared DOM scaffolding (wrapper div + hidden input) and
     * then hands over to the backend selected through the "editor" key in
     * the richtext options or DepageForm.defaultEditor.
     *
     * @param   element container .input-richtext paragraph
     **/
    async setupRichtextEditor(container) {
        // @todo merge option arrays
        var options = {};

        try {
            Object.assign(
                options,
                JSON.parse(container.getAttribute("data-textarea-options") || "{}"),
                JSON.parse(container.getAttribute("data-richtext-options") || "{}")
            );
        } catch (exception) {
        }

        var textarea = container.querySelector("textarea");

        if (!textarea) return;

        var context = {
            container: container,
            options: options,
            maxlength: parseInt(textarea.getAttribute("maxlength")) || -1,
            value: textarea.value
        };

        var wrapper = document.createElement("div");
        wrapper.className = "textarea richtext";

        var div = document.createElement("div");
        div.className = "textarea-content";
        wrapper.appendChild(div);

        textarea.after(wrapper);

        var input = document.createElement("input");
        input.type = "hidden";
        input.name = textarea.name;
        input.value = textarea.value; // old textarea value
        textarea.after(input);

        context.wrapper = wrapper;
        context.div = div;
        context.input = input;
        context.autosave = this.autosaveEnabled;
        context.form = this.form;

        textarea.remove();

        var backend = options.editor || DepageForm.defaultEditor || "squire";

        if (backend == "tiptap") {
            await this.setupTipTapEditor(context);
        } else {
            await this.setupSquireEditor(context);
        }
    }
    // }}}
    // {{{ setupSquireEditor()
    /**
     * @brief   richtext backend: replaces the textarea with a squire editor
     *
     * dompurify and squire-rte are imported dynamically, so they are
     * only loaded when the form actually contains a richtext element.
     **/
    async setupSquireEditor(context) {
        var container = context.container;
        var options = context.options;
        var wrapper = context.wrapper;
        var div = context.div;
        var input = context.input;
        var maxlength = context.maxlength;
        var autosave = context.autosave;
        var form = context.form;

        const [{ default: DOMPurify }, { default: Squire }] = await Promise.all([
            import("dompurify"),
            import("squire-rte"),
        ]);

        this.patchSquire(Squire);

        var allowedTags = [];

        for (var i = 0; i < (options.allowedTags || []).length; i++) {
            allowedTags.push(splitTag(options.allowedTags[i]).tag);
        }

        var editor = new Squire(div, {
            blockTag: 'P',
            sanitizeToDOMFragment: function(html, _self) {
                var frag = DOMPurify.sanitize(html, {
                    ALLOWED_TAGS: allowedTags,
                    ALLOWED_ATTR: ['class', 'href', 'target', 'alt', 'title', 'data-dbid'],
                    WHOLE_DOCUMENT: false,
                    RETURN_DOM: true,
                    RETURN_DOM_FRAGMENT: true,
                    ALLOWED_URI_REGEXP: /^(?:(?:(?:f|ht)tps?|mailto|tel|callto|cid|xmpp|pageref|libref|footnote):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i
                });
                // remove br's that are the last children in a node
                var brs = frag.querySelectorAll('br');
                brs.forEach(function(br) {
                    if (br.nextSibling === null) {
                        br.parentNode.removeChild(br);
                    }
                });

                return frag;
            }
        });

        editor.setHTML(context.value);

        var toolbar = new Toolbar(container, options, editor);
        var scrollTop = 0;

        wrapper.editor = editor;
        wrapper.toolbar = toolbar;

        editor.addEventListener("input", throttle(() => {
            input.value = editor.getHTML();
            input.dispatchEvent(new Event("change", { bubbles: true }));

            if (autosave) {
                this.changed();
            }

            toolbar.hide();
        }, 1000, {
            leading: true,
            trailing: true
        }));
        editor.addEventListener("focus", () => {
            wrapper.classList.add("focus");
            wrapper.scrollTop = scrollTop;
        });
        editor.addEventListener("blur", () => {
            input.value = editor.getHTML();
            input.dispatchEvent(new Event("change", { bubbles: true }));

            if (autosave) {
                this.changed();
            }

            wrapper.classList.remove("focus");

            toolbar.hide();
        });
        editor.addEventListener("select", () => {
            if (editor._isFocused) {
                var t = toolbar.show();
                if (t) t.setPositionBySelection();
            }
        });
        editor.addEventListener("keydown", (e) => {
            // @todo fix table selection and navigation on safari
            // Tab — or "^" (keycode 94 with the old keypress event)
            if (e.key != "Tab" && e.key != "^") return;

            var range = editor.getSelection();
            moveRangeBoundariesDownTree(range);

            var node = range.startContainer;
            var cell = (node.nodeType === Node.TEXT_NODE ? node.parentElement : node);
            cell = cell && cell.closest ? cell.closest("td") : null;

            if (!cell || !div.contains(cell)) return;

            var cells = Array.from(div.querySelectorAll("table td"));
            var cellIndex = cells.indexOf(cell);

            if (e.shiftKey) {
                // backwards
                cellIndex--;
            } else {
                // forward
                cellIndex++;
            }

            if (cellIndex < 0 || cellIndex >= cells.length) return;

            var r = document.createRange();
            if (cells[cellIndex].firstChild == null) {
                cells[cellIndex].appendChild(document.createTextNode(""));
            }
            r.setStartBefore(cells[cellIndex].firstChild);
            r.setEndAfter(cells[cellIndex]);

            editor.setSelection(r);
            e.stopPropagation();
            e.preventDefault();
        });

        div.addEventListener("dblclick", (e) => {
            var a = e.target.closest("a");

            if (!a) return;

            setTimeout(function() {
                var r = document.createRange();
                r.selectNode(a);
                editor.setSelection(r);
                editor.showLinkDialog(a.getAttribute("href"), function(value) {
                    editor.changeFormat(null, { tag: "a" });
                    if (value != "") {
                        editor.changeFormat({ tag: "a", attributes: { href: value } });
                    }
                    editor.focus();
                });
            }, 50);
        });
        div.addEventListener("click", (e) => {
            var cell = e.target.closest("table td:last-child");

            if (cell && div.contains(cell)) {
                editor.handleTableButtonClick.call(editor, e, cell);
            }
        });

        wrapper.addEventListener("scroll", () => {
            scrollTop = wrapper.scrollTop;
        });

        if (maxlength > 0) {
            var isMac = /Mac OS X/.test(navigator.userAgent);
            var remaining = document.createElement("span");
            remaining.className = "text-remaining";
            wrapper.appendChild(remaining);

            var updateRemaining = function() {
                var length = div.textContent.length;

                remaining.textContent = (maxlength - length) + " / " + maxlength;
                remaining.classList.toggle("error", length > maxlength);

                return length;
            };

            updateRemaining();

            editor.addEventListener("keydown", (e) => {
                var keycode = e.keyCode;

                //List of keycodes of printable characters from:
                //http://stackoverflow.com/questions/12467240/determine-if-javascript-e-keycode-is-a-printable-non-control-character
                var printable =
                    (keycode > 47 && keycode < 58)   || // number keys
                    keycode == 32 || keycode == 13   || // spacebar & return key(s) (if you want to allow carriage returns)
                    (keycode > 64 && keycode < 91)   || // letter keys
                    (keycode > 95 && keycode < 112)  || // numpad keys
                    (keycode > 185 && keycode < 193) || // ;=,-./` (in order)
                    (keycode > 218 && keycode < 223);   // [\]' (in order)

                // @todo check if text is selected because it would be overridden
                var shortcutKey = (isMac && e.metaKey) || e.ctrlKey;
                var length = updateRemaining();

                if (!shortcutKey && printable) {
                    if (length >= maxlength) {
                        e.preventDefault();
                    }
                }
            });
            for (var event of ["keyup", "contextmenu", "input"]) {
                editor.addEventListener(event, updateRemaining);
            }
        }

        emit(form, "depageForm.richtextInitialized", { container: container, editor: editor });
    }
    // }}}
    // {{{ setupTipTapEditor()
    /**
     * @brief   richtext backend: replaces the textarea with a tiptap editor
     *
     * TipTap is a schema-based editor built on ProseMirror: the
     * allowedTags option is turned into the document schema itself, so
     * unknown tags are dropped instead of filtered. The tiptap packages
     * are imported dynamically, so they are only loaded when the form
     * actually contains a richtext element using this backend.
     **/
    async setupTipTapEditor(context) {
        var container = context.container;
        var options = context.options;
        var wrapper = context.wrapper;
        var div = context.div;
        var input = context.input;
        var maxlength = context.maxlength;
        var autosave = context.autosave;
        var form = context.form;

        const [
            core,
            { Paragraph },
            { Heading },
            { StarterKit },
            link,
            table,
            { Subscript },
            { Superscript },
            { Plugin, TextSelection },
        ] = await Promise.all([
            import("@tiptap/core"),
            import("@tiptap/extension-paragraph"),
            import("@tiptap/extension-heading"),
            import("@tiptap/starter-kit"),
            import("@tiptap/extension-link"),
            import("@tiptap/extension-table"),
            import("@tiptap/extension-subscript"),
            import("@tiptap/extension-superscript"),
            import("@tiptap/pm/state"),
        ]);

        // {{{ register depage protocols with linkifyjs
        // autolink uses linkifyjs, which only accepts custom schemes
        // before it initializes itself — so register them once, before
        // the first editor is created
        if (!tiptapProtocolsRegistered) {
            tiptapProtocolsRegistered = true;

            const { registerCustomProtocol } = await import("linkifyjs");

            for (var scheme of ["pageref", "libref", "footnote"]) {
                registerCustomProtocol(scheme);
            }
        }
        // }}}

        var extensions = buildTipTapExtensions(options.allowedTags, {
            core: core,
            Paragraph: Paragraph,
            Heading: Heading,
            StarterKit: StarterKit,
            Link: link.Link,
            linkIsAllowedUri: link.isAllowedUri,
            table: table,
            Subscript: Subscript,
            Superscript: Superscript,
            Plugin: Plugin,
            TextSelection: TextSelection,
        });

        var editor = new core.Editor({
            element: div,
            extensions: extensions,
            // remove the stray line breaks the php side adds to empty
            // paragraphs — otherwise they would end up as hard breaks
            // in the document
            content: removeTrailingBreaks(context.value),
            editorProps: {
                // pasted content gets the same cleanup as the content
                // the editor is initialized with — the squire backend
                // did the same, because it sanitized every pasted
                // fragment the same way
                transformPastedHTML: (html) => removeTrailingBreaks(html),
            },
        });

        var facade = new TipTapFacade(editor, container);
        var toolbar = new Toolbar(container, options, facade);
        var scrollTop = 0;

        wrapper.editor = facade;
        wrapper.toolbar = toolbar;

        // the server stores the nodes the editor created with new
        // database ids and reports the mapping from their placeholder
        // ids to them; the editor replaces the placeholders with the
        // database ids, so that the next save can match the nodes by
        // their database id
        form.addEventListener("depageForm.autosaved", (e) => {
            var detail = e.detail || {};
            var ids = detail.result && detail.result.ids;

            if (!ids) return;

            if (!applyDbidIds(editor, ids)) return;

            // the value of the input has to be updated right away: the
            // update handler is throttled and a save in between would
            // send the placeholder ids again, which would store the node
            // a second time
            input.value = editor.getHTML();

            // the values that were sent are remembered with the new ids,
            // so that the ids alone do not trigger another save
            var sent = detail.values ? detail.values[input.name] : null;

            if (sent !== null && sent !== undefined && this.state.lastSaved) {
                var html = String(sent);

                Object.keys(ids).forEach((placeholder) => {
                    html = html
                        .split('data-dbid="' + placeholder + '"')
                        .join('data-dbid="' + ids[placeholder] + '"');
                });

                this.state.lastSaved[input.name] = html;
            }
        });

        // shows the toolbar when the editor is focused and a real
        // (non-collapsed) selection exists, hides it otherwise — the
        // squire backend behaved the same way, because squire only
        // fires "select" when the selection is not collapsed (and
        // "cursor" otherwise). Hooked into "focus" as well, so the
        // toolbar stays visible when a formatting button blurs and
        // re-focuses the editor without changing the selection.
        var updateToolbar = () => {
            if (editor.isFocused && !editor.state.selection.empty) {
                var t = toolbar.show();
                if (t) t.setPositionBySelection();
            } else {
                toolbar.hide();
            }
        };

        editor.on("update", throttle(() => {
            input.value = editor.getHTML();
            input.dispatchEvent(new Event("change", { bubbles: true }));

            if (autosave) {
                this.changed();
            }

            // hide while typing (the selection collapses) — but do
            // not hide unconditionally: the throttled trailing call
            // would otherwise remove the toolbar up to a second after
            // a content change even though the selection is still
            // there
            updateToolbar();
        }, 1000, {
            leading: true,
            trailing: true
        }));

        editor.on("focus", () => {
            wrapper.classList.add("focus");
            wrapper.scrollTop = scrollTop;

            updateToolbar();
        });
        editor.on("blur", () => {
            input.value = editor.getHTML();
            input.dispatchEvent(new Event("change", { bubbles: true }));

            if (autosave) {
                this.changed();
            }

            wrapper.classList.remove("focus");

            toolbar.hide();
        });
        editor.on("selectionUpdate", updateToolbar);

        div.addEventListener("dblclick", (e) => {
            var a = e.target.closest("a");

            if (!a) return;

            setTimeout(() => {
                var pos = editor.view.posAtDOM(a, 0);
                var range = findLinkRange(editor.state.doc, pos);

                if (range) {
                    editor.commands.setTextSelection(range);
                }

                facade.showLinkDialog(a.getAttribute("href"), (value) => {
                    if (!editor.commands.setLink) return;

                    if (value != "") {
                        // keep existing attributes like class and data-dbid
                        var attributes = Object.assign({}, range ? range.attributes : {}, { href: value });

                        editor.chain().focus().extendMarkRange("link").setLink(attributes).run();
                    } else {
                        editor.chain().focus().extendMarkRange("link").unsetLink().run();
                    }
                });
            }, 50);
        });
        div.addEventListener("click", (e) => {
            var cell = e.target.closest("table td:last-child");

            if (cell && div.contains(cell)) {
                facade.handleTableButtonClick(e, cell);
            }
        });

        wrapper.addEventListener("scroll", () => {
            scrollTop = wrapper.scrollTop;
        });

        if (maxlength > 0) {
            var isMac = /Mac OS X/.test(navigator.userAgent);
            var remaining = document.createElement("span");
            remaining.className = "text-remaining";
            wrapper.appendChild(remaining);

            var updateRemaining = function() {
                var length = div.textContent.length;

                remaining.textContent = (maxlength - length) + " / " + maxlength;
                remaining.classList.toggle("error", length > maxlength);

                return length;
            };

            updateRemaining();

            div.addEventListener("keydown", (e) => {
                var keycode = e.keyCode;

                //List of keycodes of printable characters from:
                //http://stackoverflow.com/questions/12467240/determine-if-javascript-e-keycode-is-a-printable-non-control-character
                var printable =
                    (keycode > 47 && keycode < 58)   || // number keys
                    keycode == 32 || keycode == 13   || // spacebar & return key(s) (if you want to allow carriage returns)
                    (keycode > 64 && keycode < 91)   || // letter keys
                    (keycode > 95 && keycode < 112)  || // numpad keys
                    (keycode > 185 && keycode < 193) || // ;=,-./` (in order)
                    (keycode > 218 && keycode < 223);   // [\]' (in order)

                // @todo check if text is selected because it would be overridden
                var shortcutKey = (isMac && e.metaKey) || e.ctrlKey;
                var length = updateRemaining();

                if (!shortcutKey && printable) {
                    if (length >= maxlength) {
                        e.preventDefault();
                    }
                }
            });
            for (var event of ["keyup", "contextmenu", "input"]) {
                div.addEventListener(event, updateRemaining);
            }
            editor.on("update", updateRemaining);
        }

        emit(form, "depageForm.richtextInitialized", { container: container, editor: facade });
    }
    // }}}
    // {{{ patchSquire()
    /**
     * @brief   adds depage helpers to the Squire prototype (once)
     **/
    patchSquire(Squire) {
        if (Squire.__depagePatched) return;
        Squire.__depagePatched = true;

        // {{{ Squire.replaceBlock
        Squire.prototype.replaceBlock = function(tag) {
            var result = splitTag(tag);

            this.modifyBlocks(function(frag) {
                var newFrag = frag.ownerDocument.createDocumentFragment();
                var i, l;

                for (i = 0, l = frag.children.length; i < l; i += 1) {
                    var el = frag.ownerDocument.createElement(result.tag);
                    while (frag.children[i].childNodes.length > 0) {
                        var node = frag.children[i].childNodes[0];
                        if (node.nodeName == "LI") {
                            // if element is a list add it's children instead
                            while (node.childNodes.length > 0) {
                                el.appendChild(node.childNodes[0]);
                            }
                            node.parentNode.removeChild(node);
                        } else {
                            el.appendChild(node);
                        }
                    }
                    newFrag.appendChild(el);
                }

                if (typeof result.className != 'undefined') {
                    for (i = 0; i < newFrag.children.length; i++) {
                        newFrag.children[i].className = result.className;
                    }
                }

                return newFrag;
            }, this.getSelection());

            return this.focus();
        };
        // }}}
        // {{{ Squire.makeHeadline
        Squire.prototype.makeHeadline = function(level) {
            if (typeof level == 'undefined') {
                level = 1;
            }
            return this.replaceBlock("h" + level);
        };
        // }}}
        // {{{ Squire.removeHeadline
        Squire.prototype.removeHeadline = function() {
            return this.replaceBlock("p");
        };
        // }}}
        // {{{ Squire.getPosBySelection()
        Squire.prototype.getPosBySelection = function(offsetTop, offsetLeft) {
            var range = this.getSelection();
            var rects = range.getClientRects();
            var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            var scrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft;

            offsetTop = offsetTop || 0;
            offsetLeft = offsetLeft || 0;

            // @todo check if mouse position is inside rect and position the toolbar next to mouse
            // @todo check that toolbar stays in visible area of window
            // @todo change position only if necessary (if selection has changed)
            // @todo adjust toolbar position on iOS because of native popup

            var left = 1000000000;
            var right = 0;
            var top = 0;
            for (var i = 0; i < rects.length; i++) {
                left = Math.min(left, rects[i].left);
                right = Math.max(right, rects[i].right);
            }
            if (rects[0]) {
                top = rects[0].top + scrollTop - offsetTop;
            }
            left = left + (right - left) * 0.5 + scrollLeft - offsetLeft;

            if (top < 60) {
                top = 60;
            }

            return {
                top: top,
                left: left
            };
        };
        // }}}
        // {{{ Squire.showLinkDialog()
        if (typeof Squire.prototype.showLinkDialog === 'undefined') {
            Squire.prototype.showLinkDialog = function(href, callback) {
                // dispatched on the editable root, so it bubbles up to the
                // form and the document; the embedding application (e.g.
                // the depage-cms interface) listens for it and opens a
                // dialog — same contract as the tiptap backend
                emit(this._root, "depageForm.showLinkDialog", {
                    href: href,
                    callback: callback,
                    editor: this
                }, true);
            };
        }
        // }}}
        // {{{ Squire.setEditable()
        Squire.prototype.setEditable = function(editable) {
            this._root.setAttribute("contenteditable", editable ? "true" : "false");
        };
        // }}}
        // {{{ Squire.handleTableButtonClick()
        Squire.prototype.handleTableButtonClick = function(e, cell) {
            cell = cell || e.currentTarget;

            // the "+" and "-" buttons are rendered through css inside the
            // right padding-area of the last cell (td:last-child:before /
            // :after at right: 0 with padding-right: 1em on the cell);
            // compare the click position against the content-box width —
            // the equivalent of the old jquery $cell.width() — so that
            // clicks in the padding-area count as button clicks
            var cellStyle = getComputedStyle(cell);
            var contentWidth = cell.clientWidth
                - parseFloat(cellStyle.paddingLeft)
                - parseFloat(cellStyle.paddingRight);

            if (e.offsetX < contentWidth) return;

            var currentRow = cell.parentElement;

            if (e.offsetY < 20) {
                var newRow = currentRow.cloneNode(true);

                Array.from(newRow.children).forEach((td) => {
                    td.textContent = "";

                    var p = td.ownerDocument.createElement("p");
                    p.appendChild(td.ownerDocument.createElement("br"));
                    td.appendChild(p);
                });

                currentRow.after(newRow);
            } else if (e.offsetY < 40 && currentRow.parentElement.children.length > 1) {
                currentRow.remove();
            }

            return false;
        };
        // }}}
    }
    // }}}
    // {{{ setupSelects()
    /**
     * @brief   enhances selects with tom-select
     *
     * tom-select is imported dynamically, so it is only loaded when
     * the form actually contains a select-element. Starts synchronously
     * so the rest of the setup is not delayed by an extra microtask.
     **/
    setupSelects() {
        var selects = this.form.querySelectorAll("select");

        if (selects.length == 0) return;

        import("tom-select").then(({ default: TomSelect }) => {
            selects.forEach((select) => {
                var maxItems = select.getAttribute("data-max-items");
                var options = {
                    persist: false,
                };

                // only override maxItems when it is set explicitly;
                // without it tom-select itself defaults to 1 item for
                // single <select> elements (and null for multiple ones)
                if (maxItems !== null) {
                    options.maxItems = parseInt(maxItems);
                }

                // the remove-button only makes sense where an item can
                // actually be removed: on multiple selects or on single
                // selects that have an empty option to fall back to.
                // unlike selectize's remove_button, tom-select's version
                // would otherwise also render it on single selects,
                // which could then be cleared to a value that the plain
                // <select> can never have
                var hasEmptyOption = Array.from(select.options).some((option) => option.value === "");

                if (select.multiple || hasEmptyOption) {
                    options.plugins = ['remove_button'];
                }

                new TomSelect(select, options);
            });
        }).catch((exception) => {
            console.warn("depage-forms: could not initialize tom-select", exception);
        });
    }
    // }}}
    /* }}} */
}
/* }}} */

// {{{ instances
var instances = new WeakMap();
// }}}
// {{{ registerBeforeUnload()
var beforeUnloadRegistered = false;

/**
 * @brief   saves all autosave-forms before the page is unloaded
 *
 * Warns the user when a form is still saving.
 **/
function registerBeforeUnload() {
    if (beforeUnloadRegistered) return;
    beforeUnloadRegistered = true;

    window.addEventListener("beforeunload", (e) => {
        var isSaving = false;

        document.querySelectorAll("form.depage-form").forEach((form) => {
            var instance = instances.get(form);

            if (instance && instance.autosaveEnabled && !instance.state.submitting) {
                instance.autosave();

                if (instance.state.saving) {
                    isSaving = true;
                }
            }
        });

        if (isSaving) {
            e.preventDefault();

            // needs to be set for some browsers to show the dialog
            e.returnValue = "The form is still saving. You really want to leave this page?";
        }
    });
}
// }}}
// {{{ setupForm()
/**
 * @brief   initializes a single form (idempotent)
 *
 * @param   element form form element, selector or jQuery object
 * @return  DepageForm
 **/
export function setupForm(form) {
    form = getElement(form);

    if (!form) return null;
    if (instances.has(form)) return instances.get(form);

    var instance = new DepageForm(form);

    instances.set(form, instance);
    form.setAttribute("data-depage-form-initialized", "true");

    registerBeforeUnload();

    instance.ready = instance.init();

    return instance;
}
// }}}
// {{{ setup()
/**
 * @brief   initializes all .depage-form forms inside root (idempotent)
 *
 * @param   element root element, selector or jQuery object (optional, defaults to document)
 * @return  array DepageForm instances
 **/
export function setup(root) {
    root = root ? getElement(root) : document;

    var forms = root.matches && root.matches(".depage-form")
        ? [root]
        : Array.from(root.querySelectorAll(".depage-form"));

    return forms.map((form) => setupForm(form));
}
// }}}

DepageForm.setup = setup;
DepageForm.setupForm = setupForm;

/**
 * @brief   default richtext editor backend: "squire" or "tiptap"
 *
 * can be overridden per form through the "editor" key in
 * data-richtext-options.
 **/
DepageForm.defaultEditor = "squire";

export default DepageForm;

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
