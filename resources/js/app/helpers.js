import { DateTime } from 'luxon'

export const formatDate = (dateString, format, separator) => {
    if (!dateString) {
        return null
    }

    let date = DateTime.fromFormat(dateString, format)

    if (date.isValid) {
        return date.toFormat(format.replace(/-/g, separator))
    }

    date = DateTime.fromISO(dateString)

    if (date.isValid) {
        return date.toFormat(format.replace(/-/g, separator))
    }

    date = DateTime.fromFormat(dateString, 'yyyy-MM-dd HH:mm:ss')

    if (date.isValid) {
        return date.toFormat(format.replace(/-/g, separator))
    }

    return 'Invalid DateTime'
}

const escapeRegex = string => {
    if (Array.isArray(string) || typeof string === 'object' || typeof string === 'boolean') {
        return ''
    }

    string = String(string)

    return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")
}

export const applyFilterRegex = (string, global, self = []) => {
    if (Array.isArray(string) || typeof string === 'object' || typeof string === 'boolean') {
        return ''
    }

    if (Array.isArray(global) || typeof global === 'object' || typeof global === 'boolean') {
        global = ''
    }

    self = Array.from(self)
    string = String(string)
    global = String(global)

    const regexParts = []

    if (global !== '') {
        regexParts.push(escapeRegex(global))
    }

    self.forEach(value => {
        if (Array.isArray(value) || typeof value === 'object' || typeof value === 'boolean') {
            return
        }

        value = String(value)
        if (value !== '') {
            regexParts.push(value)
        }
    })

    if (!regexParts.length) {
        return string
    }

    return string.replace(new RegExp(regexParts.join('|'), 'gi'), `<span class="regex-result">$&</span>`)
}

