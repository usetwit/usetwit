import axios from 'axios'
import { ref } from 'vue'
import { toast } from 'vue3-toastify'

export default function useAxios(url, params = {}, method = 'post') {
    const data = ref(null)
    const errors = ref({
        fields: [],
        list: '',
        raw: null,
        message: '',
    })
    const status = ref(null)

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
            status.value = response.status
            errors.value = {
                fields: [],
                list: '',
                raw: null,
                message: '',
            }

        } catch (error) {

            if (error.response?.data?.errors) {
                const responseErrors = error.response.data.errors
                errors.value.fields = Object.keys(responseErrors)
                errors.value.list = Object.values(responseErrors).flat().join('\n')
            }

            errors.value.raw = error
            status.value = error.response.status
            errors.value.message = error.message

            toast.error(`<b>${errors.value.message}</b>\n${errors.value.list}`, {
                dangerouslyHTMLString: true,
            })
        }
    }

    return { data, status, errors, getResponse }
}
