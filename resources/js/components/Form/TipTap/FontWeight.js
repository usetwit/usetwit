import { Mark } from '@tiptap/core'

export default Mark.create({
    name: 'fontWeight',

    addOptions() {
        return {
            weights: [
                'thin', 'extralight', 'light', 'normal', 'medium',
                'semibold', 'bold', 'extrabold', 'black'
            ],
            HTMLAttributes: {},
        }
    },

    parseHTML() {
        return this.options.weights.map(weight => ({
            tag: `span.font-${weight}`,
        }))
    },

    renderHTML({ mark, HTMLAttributes }) {
        return [
            'span',
            {
                ...HTMLAttributes,
                class: `font-${mark.attrs.weight}`,
            },
            0
        ]
    },

    addAttributes() {
        return {
            weight: {
                default: 'normal',
                parseHTML: element => element.className.match(/font-(\w+)/)?.[1] || 'normal',
                renderHTML: attributes => {
                    if (!attributes.weight) return {}
                    return { class: `font-${attributes.weight}` }
                },
            },
        }
    },

    addCommands() {
        return {
            toggleFontWeight: (weight) => ({ commands }) => {
                return commands.toggleMark(this.name, { weight })
            },
        }
    },
})
