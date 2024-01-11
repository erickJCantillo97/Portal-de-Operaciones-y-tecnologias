<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { ChatBubbleLeftEllipsisIcon, TagIcon, UserCircleIcon } from '@heroicons/vue/20/solid'
import CommentForm from '@/Components/CommentForm.vue'
import Loading from '@/Components/Loading.vue'
import Menu from 'primevue/menu'
import Button from 'primevue/button'
import Skeleton from 'primevue/skeleton'
import Moment from 'moment'

const props = defineProps({
  quoteId: Number
})

const action = ref(0)
const comment = ref({})
const commentSelect = ref()
const menu = ref()
const loadingStatus = ref(true)

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
          console.log(action.value)
        }
      },
      {
        label: 'Editar',
        icon: 'pi pi-pencil',
        command: () => {
          action.value = 3
          comment.value = commentSelect.value
          console.log(action.value)
        }
      },
      {
        label: 'Eliminar',
        icon: 'pi pi-trash',
        command: () => {
          action.value = 1
          router.delete(route('comment.destroy', comment.value.id), {
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

const comments = ref([])

const getComments = () => {
  comment.value = {}
  action.value = 0
  axios.get(route('comment.index', { id: props.quoteId }))
    .then((res) => {
      comments.value = res.data.comments
      orderByLatest()
      // loadingStatus.value = false
    })
}

//#region LifeCycles Hooks
onMounted(() => {
  getComments()
})
//#endregion

const orderByLatest = () => {
  let container = document.querySelector('#conversation')
  container.scrollTop = container.scrollHeight
}

const format_ES_Date = (date) => {
  return new Date(date).toLocaleString('es-CO',
    { day: '2-digit', month: 'long', year: 'numeric', weekday: "long" })
}
</script>

<template>
  <Menu ref="menu" id="overlay_menu" :model="overlayOptions" :popup="true" />
  <div class="flow-root">
    <div v-chat-scroll id="conversation"
      class="max-h-[258px] overflow-y-auto scroll-p-0 scroll-m-0 scroll-smooth p-6 mt-4 shadow-md rounded-lg">
      <!-- <div class="flex w-full mb-3">
        <Skeleton shape="circle" size="3rem" class="mr-2"></Skeleton>
        <div>
          <Skeleton width="10rem" class="mb-2"></Skeleton>
          <Skeleton width="5rem" class="mb-2"></Skeleton>
          <div>
            <Skeleton height=".5rem" class="mb-2"></Skeleton>
          </div>
        </div>
      </div> -->
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
                    <div class="flex justify-end">
                      <p class="mt-0.5 mr-2 text-sm text-gray-500">{{ Moment(commentItem.date).format('DD/MM/YY') }}</p>
                      <Button @click="toggle($event, commentItem)" v-if="commentItem.user_id === $page.props.auth.user.id"
                        class="!size-4" type="button" icon="pi pi-ellipsis-v" aria-haspopup="true"
                        aria-controls="overlay_menu" text />
                    </div>
                  </div>
                </div>
                <div class="mt-2 text-sm text-gray-700">
                  <p>{{ commentItem.message }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="relative pb-1 ml-12 bg-gray-200 rounded-lg mb-2" v-for="response in commentItem.responses">
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
                    <div class="flex justify-end">
                      <p class="mt-0.5 mr-2 text-sm text-gray-500">
                        {{ Moment(commentItem.date).format('DD/MM/YY') }}
                      </p>
                      <Button @click="toggle($event, commentItem)" v-if="commentItem.user_id === $page.props.auth.user.id"
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
