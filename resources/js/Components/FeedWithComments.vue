<script setup>
import { ref, onMounted } from 'vue'
import { ChatBubbleLeftEllipsisIcon, TagIcon, UserCircleIcon } from '@heroicons/vue/20/solid'
import CommentForm from '@/Components/CommentForm.vue'
import Loading from '@/Components/Loading.vue'
import Menu from 'primevue/menu'
import Button from 'primevue/button'

const props = defineProps({
  quoteId: Number
})

const menu = ref()
const loadingStatus = ref(true)

const overlayOptions = ref([
  {
    label: 'Opciones',
    items: [
      {
        label: 'Responder',
        icon: 'pi pi-reply'
      },
      {
        label: 'Editar',
        icon: 'pi pi-pencil'
      },
      {
        label: 'Eliminar',
        icon: 'pi pi-trash'
      },
    ]
  }
])

const toggle = (event, index) => {
  menu.value.toggle(event)
}

const comments = ref([])

const getComments = () => {
  axios.get(route('comment.index', { id: props.quoteId })).then(
    (res) => {
      comments.value = res.data.comments
      // loadingStatus.value = false
    })
}
onMounted(() => {
  getComments()
})

const format_ES_Date = (date) => {
  return new Date(date).toLocaleString('es-CO',
    { day: '2-digit', month: 'long', year: 'numeric', weekday: "long" })
}
</script>

<template>
  <Menu ref="menu" id="overlay_menu" :model="overlayOptions" :popup="true" />
  <div class="flow-root">
    <ul role="list" class="max-h-[258px] overflow-y-auto scroll-smooth p-6 mt-4 shadow-md rounded-lg">
      <li v-for="(commentItem, commentItemIdx) in comments">
        <div class="relative pb-1">
          <span v-if="commentItemIdx !== comments.length - 1"
            class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
          <div class="relative flex items-start space-x-3">
            <div class="relative">
              <img class="flex h-10 w-10 items-center justify-center rounded-full object-cover bg-gray-400 ring-8 ring-white"
                :src="commentItem.user_photo" alt="profile_photo" />
              <span class="absolute -bottom-0.5 -right-1 rounded-tl bg-white px-0.5 py-px">
                <ChatBubbleLeftEllipsisIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
              </span>
            </div>
            <div class="min-w-0 flex-1">
              <div>
                <div class="text-sm flex justify-between">
                  <a class="font-medium text-gray-900">
                    {{ commentItem.user_name }}
                  </a>
                  <div class="flex justify-end">
                    <Button @click="toggle($event, commentItem)" class="!size-4" type="button" icon="pi pi-ellipsis-v"
                      aria-haspopup="true" aria-controls="overlay_menu" text />
                  </div>
                </div>
                <p class="mt-0.5 text-sm text-gray-500">Comentado el: {{ format_ES_Date(commentItem.date) }}</p>
              </div>
              <div class="mt-2 text-sm text-gray-700">
                <p>{{ commentItem.message }}</p>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
    <CommentForm :quoteId="quoteId" @addComment="getComments" class="bottom-0 left-0 right-0" />
  </div>
</template>
