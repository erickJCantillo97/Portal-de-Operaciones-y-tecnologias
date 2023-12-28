<script setup>
import { ref } from 'vue'
import Button from 'primevue/button'
import { defineEmits } from 'vue'

const emit = defineEmits(['addComment'])


const message = ref('')
const props = defineProps({
  quoteId: Number
})

const submit = () => {
  try {
    axios.post(route('comment.store', { commentable_id: props.quoteId, message: message.value }))
      .then(res => {
        message.value = ''
        emit('addComment')
      })
  } catch (error) {
    console.log(error)
  }
}
</script>

<template>
  <!-- New comment form -->
  <div class="mt-4 flex gap-x-3">
    <img :src="$page.props.auth.user.photo" class="size-10 rounded-full object-cover" />
    <form @submit.prevent="submit()" class="relative flex-auto">
      <div
        class="overflow-hidden rounded-lg shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-indigo-600">
        <label for="comment" class="sr-only italic">Escribir comentarios...</label>
        <textarea required v-model="message" rows="4" name="comment" id="comment"
          class="block w-full resize-none border-0 bg-transparent py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
          placeholder="Escriba su comentario..." />
      </div>
      <div class="flex justify-end items-center mt-2">
        <Button type="submit" icon="pi pi-send" label="Enviar" severity="primary" size="small" outlined />
      </div>
    </form>
  </div>
</template>
