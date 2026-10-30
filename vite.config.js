/**
 * @file    vite.config.js
 * @brief   builds the standalone iife bundles into dist/
 *
 *     bun run build:js
 *
 * builds two bundles:
 *     - dist/depage-form.js         vanilla, no dependencies
 *     - dist/depage-form-jquery.js  jquery wrapper (jquery itself is
 *                                   external and expected as global)
 *
 * the css is built separately from src/sass via bun (build:css).
 **/
import { defineConfig } from "vite";

export default defineConfig(({ mode }) => {
    const jquery = mode === "jquery";

    return {
        build: {
            outDir: "dist",
            // css and the other bundle live in dist as well
            emptyOutDir: false,
            sourcemap: true,
            lib: {
                entry: jquery ? "src/entries/standalone-jquery.js" : "src/entries/standalone.js",
                formats: ["iife"],
                name: jquery ? "depageFormJquery" : "depageForm",
                fileName: () => (jquery ? "depage-form-jquery.js" : "depage-form.js"),
            },
            rollupOptions: {
                external: jquery ? ["jquery"] : [],
                output: {
                    globals: {
                        jquery: "jQuery",
                    },
                },
            },
        },
    };
});
