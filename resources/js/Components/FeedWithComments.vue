<script setup>
import { ChatBubbleLeftEllipsisIcon } from '@heroicons/vue/20/solid'
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Button from 'primevue/button'
import ChatSkeleton from '@/Components/ChatSkeleton.vue'
import CommentForm from '@/Components/CommentForm.vue'
import Menu from 'primevue/menu'
import Moment from 'moment'
import NoContentToShow from '@/Components/NoContentToShow.vue'

const props = defineProps({
  quoteId: Number
})

const action = ref(0)
const comment = ref({})
const comments = ref([])
const commentSelect = ref()
const menu = ref()
const showChatSkeleton = ref(true)
const showNoContent = ref(false)

const overlayOptions = ref([
  {
    label: 'Opciones',
    items: [
      {
        label: 'Responder',
        icon: 'pi pi-reply',
        command: () => {
          action.value = 2
          comment.value = commentSelect.value
        }
      },
      {
        label: 'Editar',
        icon: 'pi pi-pencil',
        command: () => {
          action.value = 3
          comment.value = commentSelect.value
        }
      },
      {
        label: 'Eliminar',
        icon: 'pi pi-trash',
        command: () => {
          action.value = 1
          router.delete(route('comment.destroy', commentSelect.value.id), {
            onSuccess: () => {
              getComments()
            },
            onError: (errors) => {
              console.log('error: ' + errors)
            }
          })
        }
      },
    ]
  }
])

const toggle = (event, commentItem) => {
  menu.value.toggle(event)
  commentSelect.value = commentItem
}

const reply = (commentItem) => {
  comment.value = commentItem
  action.value = 2
}

const getComments = async () => {
  comment.value = {}
  action.value = 0

  await axios.get(route('comment.index', { id: props.quoteId }))
    .then((res) => {
      comments.value = res.data.comments
      showChatSkeleton.value = false
      comments.value == 0 ? showNoContent.value = true : comments.value
    })
}

//#region LifeCycles Hooks
onMounted(() => {
  getComments()
})
//#endregion

//#region Utilities
// const orderByLatest = () => {
//   let container = document.querySelector('#conversation')
//   container.scrollTop = container.scrollHeight
// }

// const format_ES_Date = (date) => {
//   return new Date(date).toLocaleString('es-CO',
//     { day: '2-digit', month: 'long', year: 'numeric', weekday: "long" })
// }
//#endregion
</script>

<template>
  <Menu ref="menu" id="overlay_menu" :model="overlayOptions" :popup="true" />
  <div class="flow-root">
    <ChatSkeleton v-if="showChatSkeleton" />
    <NoContentToShow subject="Esta estimación no tiene comentarios" v-if="comments.length == 0 && !showChatSkeleton" />
    <!--COMENTARIOS-->
    <div v-chat-scroll
      class="max-h-[258px] overflow-y-auto scroll-p-0 scroll-m-0 scroll-smooth p-6 mt-4 shadow-md rounded-lg">
      <ul role="list">
        <li v-for="(commentItem, commentItemIdx) in comments">
          <div class="relative pb-1">
            <span v-if="commentItemIdx !== comments.length - 1"
              class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
            <div class="relative flex items-start space-x-3">
              <div class="relative">
                <img
                  class="flex size-10 items-center justify-center rounded-full object-cover bg-gray-400 ring-8 ring-white"
                  :src="commentItem.user_photo" alt="profile_photo" />
                <span class="absolute -bottom-0.5 -right-1 rounded-tl bg-white px-0.5 py-px">
                  <ChatBubbleLeftEllipsisIcon class="size-5 text-gray-400" aria-hidden="true" />
                </span>
              </div>
              <div class="min-w-0 flex-1 p-2">
                <div>
                  <div class="text-sm flex justify-between">
                    <a class="font-medium text-gray-900">
                      {{ commentItem.user_name }}
                    </a>
                    <div class="flex justify-end items-center">
                      <p class="mt-0.5 mr-2 text-sm text-gray-500">{{ Moment(commentItem.date).format('DD/MM/YY') }}</p>
                      <Button @click="toggle($event, commentItem)" v-if="commentItem.user_id === $page.props.auth.user.id"
                        class="!size-4" type="button" icon="pi pi-ellipsis-v" aria-haspopup="true"
                        aria-controls="overlay_menu" text />
                      <Button class="!size-4 rotate-180 p-2" type="button" icon="pi pi-reply" aria-haspopup="true"
                        aria-controls="overlay_menu" text v-else @click="reply(commentItem)">
                      </Button>
                    </div>
                  </div>
                </div>
                <div class="mt-2 text-sm text-gray-700">
                  <p>{{ commentItem.message }}</p>
                </div>
              </div>
            </div>
          </div>

          <!--RESPUESTAS-->
          <div class="relative pb-1 ml-12 bg-gray-200 rounded-lg mb-2" v-for="response in commentItem.responses">
            <div class="relative flex items-start space-x-3">
              <div class="relative">
                <img
                  class="flex size-10 items-center justify-center rounded-full object-cover bg-gray-400 ring-8 ring-white"
                  :src="response.user_photo" alt="profile_photo" />
                <span class="absolute -bottom-0.5 -right-1 rounded-tl bg-white px-0.5 py-px">
                  <ChatBubbleLeftEllipsisIcon class="size-5 text-gray-400" aria-hidden="true" />
                </span>
              </div>
              <div class="min-w-0 flex-1 p-2">
                <div>
                  <div class="text-sm flex justify-between">
                    <a class="font-medium text-gray-900">
                      {{ response.user_name }}
                    </a>
                    <div class="flex justify-end">
                      <p class="mt-0.5 mr-2 text-sm text-gray-500">
                        {{ Moment(response.date).format('DD/MM/YY') }}
                      </p>
                      <Button @click="toggle($event, response)" v-if="response.user_id === $page.props.auth.user.id"
                        class="!size-4" type="button" icon="pi pi-ellipsis-v" aria-haspopup="true"
                        aria-controls="overlay_menu" text />
                    </div>
                  </div>
                </div>
                <div class="mt-2 text-sm text-gray-700">
                  <p>{{ response.message }}</p>
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <CommentForm :quoteId="quoteId" @addComment="getComments" :comment="comment" v-model:actions="action"
      class="bottom-0 left-0 right-0" />
  </div>
</template>
