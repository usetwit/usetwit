import axios from 'axios'
import {ref} from 'vue'

export function useAxios(url, params = {}, method = 'post') {
    const data = ref(null)
    const errors = ref({
        fields: [],
        list: '',
        raw: null,
        status: 0,
        message: '',
    })

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

    const getResponse = async () => {
        try {
            const response = await axios({
                method,
                url,
                data: params,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            data.value = response.data

        } catch (error) {

            if (error.response && error.response.data && error.response.data.errors) {
                const responseErrors = error.response.data.errors
                errors.value.fields = Object.keys(responseErrors)
                errors.value.list = Object.values(responseErrors).flat().join('\n')
            }

            errors.value.raw = error
            errors.value.status = error.response.status
            errors.value.message = error.message
        }
    }

    return {data, errors, getResponse}
}
