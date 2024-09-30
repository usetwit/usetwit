import { DateTime } from 'luxon'
import { u } from "../../../public/build/assets/menuStore-DJKc6H4o.js";

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
        global = null
    }

    self = Array.from(self)
    string = String(string)
    global = String(global)
    const regexParts = []

    if (global) {
        const escapedGlobal = escapeRegex(global)
        if (escapedGlobal) {
            regexParts.push(escapedGlobal)
        }
    }

    self.forEach(value => {
        const escapedValue = escapeRegex(value)
        if (escapedValue) {
            regexParts.push(escapedValue)
        }
    })

    if (!regexParts.length) {
        return string
    }

    return string.replace(new RegExp(regexParts.join('|'), 'gi'), `<span class="regex-result">$&</span>`)
}

