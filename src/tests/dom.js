/**
 * @file    src/tests/dom.js
 * @brief   shared happy-dom setup for the js tests
 *
 * bun runs all test files in the same process, so the global
 * registrator may only be called once.
 **/
import { GlobalRegistrator } from "@happy-dom/global-registrator";
import { spawnSync } from "node:child_process";

try {
    GlobalRegistrator.register();
} catch (exception) {
    // already registered by another test file
}

/**
 * @brief   renders a depage-forms test fixture with php
 *
 * @param   string file fixture script name (defaults to render-fixture.php)
 **/
export function renderFixture(file = "render-fixture.php") {
    const fixture = spawnSync("php", ["src/tests/" + file], {
        cwd: process.cwd(),
    }).stdout.toString();

    if (!fixture.includes("depage-form")) {
        throw new Error("could not render php fixture");
    }

    return fixture;
}

/* vim:set ft=javascript sw=4 sts=4 fdm=marker : */
