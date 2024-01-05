<script setup>
import { ref } from 'vue'
import Button from 'primevue/button'

const emit = defineEmits(['addComment'])

const message = ref('')
const actions = defineModel('actions')
const closeResponse = ref(false)

const props = defineProps({
    quoteId: Number,
    comment: Object,
})

const closeResponseComponent = () => {
    closeResponse.value = true

    // if(props.actions == 2) {
    //   closeResponse.value
    // }

    // if(props.actions != 2 || props.actions == null) {
    //   closeResponse.value = true
    // }
}

const submit = () => {
    try {
        if (props.actions == 1) {
            axios.put(route('comment.update', props.message.id), { commentable_id: props.quoteId, message: message.value })
                .then(res => {
                    message.value = ''
                    emit('addComment')
                })
        } else {
            axios.post(route('comment.store', { commentable_id: props.quoteId, message: message.value, response_id: props.comment?.id ?? null }))
                .then(res => {
                    message.value = ''
                    emit('addComment')
                })
        }
    } catch (error) {
        console.log(error)
    }
}
</script>

<template>
    <!-- New comment form -->
    <div class="mt-4 flex gap-x-3">
        <img :src="$page.props.auth.user.photo" class="size-10 rounded-full object-cover" />
        <form @submit.prevent="submit()" class="flex-auto">
            <div v-if="!closeResponse"> <!--true-->
                <div v-if="actions == 2"
                    class="w-full p-1 rounded-t-lg rounded-l-lg text-sm bg-gray-200 italic mb-2 border-l-8 border-blue-900">
                    <div class="relative">
                        <i @click="actions = null"
                            class="fa-solid fa-xmark absolute top-0.5 right-0.5 text-gray-400 cursor-pointer"></i>
                    </div>
                    <div class="truncate max-w-96 ">
                        <div class="text-xs text-blue-900 uppercase font-bold">
                            {{ comment.user_name }}
                        </div>
                        {{ comment.message }}
                    </div>
                </div>
            </div>

            <div class="overflow-hidden flex gap-x-3">
                <div class="w-10/12">
                    <label for="comment" class="sr-only italic">Escribir comentarios...</label>
                    <textarea @keypress.enter="submit" required v-model="message" rows="2" name="comment" id="comment"
                        class="resize-none w-full rounded-lg shadow-sm ring-1 focus:ring-inset ring-inset ring-gray-300 border-0  bg-transparent py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-1 sm:text-sm focus:ring-indigo-300"
                        placeholder="Escriba su comentario..." />
                </div>
                <div class="mt-2">
                    <Button icon="pi pi-send" type="submit" label="Enviar" severity="primary" size="small" outlined />
                </div>
            </div>
        </form>
    </div>
</template>
