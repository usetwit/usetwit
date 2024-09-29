import { DateTime } from 'luxon'

export const formatDate = (dateString, format, separator) => {
    if (!dateString){
        return null
    }

    let date = DateTime.fromFormat(dateString, 'yyyy-MM-dd')

    if(date.isValid){
        return date.toFormat(format.replace(/-/g, separator))
    }

    date = DateTime.fromISO(dateString)

    if(date.isValid){
        return date.toFormat(format.replace(/-/g, separator))
    }

    date = DateTime.fromFormat(dateString, 'yyyy-MM-dd HH:mm:ss')

    if(date.isValid){
        return date.toFormat(format.replace(/-/g, separator))
    }

    return 'Invalid DateTime'
}
