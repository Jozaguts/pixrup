// eslint.config.js
// NOTE: Flat config requires a standalone object ONLY for "ignores".

import { defineConfig } from 'eslint/config';
import prettier from 'eslint-config-prettier/flat';
import vue from 'eslint-plugin-vue';
import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript';

export default defineConfig([
    {
        // ✅ Standalone ignore block (no other properties here)
        // In Flat Config, prefer ** globbing for nested patterns.
        ignores: [
            // Vendor/bundles/minified
            'node_modules/**',
            'vendor/**', // Laravel vendor
            'public/**', // public builds
            'resources/theme/**',
            'dist/**',
            'build/**',
            '**/*.min.js',
            // Optional: ignore config files you do not want ESLint to touch
            // 'tailwind.config.js',
            // 'vite.config.js',
        ],
    },

    // Vue + TS recommended (if your project uses it)
    ...defineConfigWithVueTs(
        vue.configs['flat/essential'],
        vueTsConfigs.recommended,
    ),

    // Project-specific rules
    {
        rules: {
            'vue/multi-word-component-names': 'off',
            '@typescript-eslint/no-explicit-any': 'off',
            // Optional: relax unused-expression checks if you rely on libraries with bare IIFEs
            // '@typescript-eslint/no-unused-expressions': ['error', { allowShortCircuit: true, allowTernary: true, allowTaggedTemplates: true }],
        },
    },

    // Disable rules that conflict with Prettier formatting
    prettier,
]);
