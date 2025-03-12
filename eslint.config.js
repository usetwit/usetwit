import globals from 'globals';
import pluginJs from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';

/** @type {import('eslint').Linter.FlatConfig[]} */
export default [
    {
        files: ['**/*.{js,mjs,cjs,vue}'],
        languageOptions: {
            globals: globals.browser,
        },
        rules: {
            'comma-dangle': ['error', 'always-multiline'],
            'semi': ['error', 'always'],
            'vue/multi-word-component-names': 'off',
        },
    },
    pluginJs.configs.recommended,
    ...pluginVue.configs['flat/essential'],
];
