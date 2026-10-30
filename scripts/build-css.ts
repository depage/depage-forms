/**
 * @file    scripts/build-css.ts
 * @brief   compiles the sass sources in src/sass into dist/
 *
 * runs with bun, which executes this typescript file directly:
 *
 *     bun run build:css
 **/
import * as sass from "sass";
import { mkdir, writeFile } from "node:fs/promises";

const files = [
    "depage-forms",
    "depage-richtext",
];

await mkdir("dist", { recursive: true });

for (const name of files) {
    const result = await sass.compileAsync(`src/sass/${name}.scss`, {
        style: "expanded",
        loadPaths: ["src/sass"],
    });

    await writeFile(`dist/${name}.css`, result.css);

    console.log(`built dist/${name}.css`);
}

/* vim:set ft=typescript sw=4 sts=4 fdm=marker : */
