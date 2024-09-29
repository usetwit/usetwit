import { DateTime } from 'luxon'

export const formatDate = (dateString, format, separator) => {
    if (!dateString){
        return null
    }

    return DateTime.fromFormat(dateString, 'yyyy-MM-dd').toFormat(format.replace(/-/g, separator))
}
