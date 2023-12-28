<script setup>
import { ref } from 'vue'
import { ChatBubbleLeftEllipsisIcon, TagIcon, UserCircleIcon } from '@heroicons/vue/20/solid'
import CommentForm from '@/Components/CommentForm.vue'
import Menu from 'primevue/menu'
import Button from 'primevue/button'

const props = defineProps({
  quoteId: Number
})

const menu = ref()
const items = ref([
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

const activity = [
  {
    id: 1,
    type: 'comment',
    person: { name: 'Eduardo Benz', href: '#' },
    imageUrl:
      'https://images.unsplash.com/photo-1520785643438-5bf77931f493?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=256&h=256&q=80',
    comment:
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tincidunt nunc ipsum tempor purus vitae id. Morbi in vestibulum nec varius. Et diam cursus quis sed purus nam. ',
    date: 'Hace 6d',
  },
  {
    id: 2,
    type: 'assignment',
    person: { name: 'Hilary Mahy', href: '#' },
    assigned: { name: 'Kristin Watson', href: '#' },
    date: 'Hace 2d',
  },
  {
    id: 3,
    type: 'tags',
    person: { name: 'Hilary Mahy', href: '#' },
    tags: [
      { name: 'Bug', href: '#', color: 'fill-red-500' },
      { name: 'Accessibility', href: '#', color: 'fill-indigo-500' },
    ],
    date: 'Hace 6h',
  },
  {
    id: 4,
    type: 'comment',
    person: { name: 'Jason Meyers', href: '#' },
    imageUrl:
      'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=256&h=256&q=80',
    comment:
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tincidunt nunc ipsum tempor purus vitae id. Morbi in vestibulum nec varius. Et diam cursus quis sed purus nam. Scelerisque amet elit non sit ut tincidunt condimentum. Nisl ultrices eu venenatis diam.',
    date: 'Hace 2h',
  },
]
</script>

<template>
  <Menu ref="menu" id="overlay_menu" :model="items" :popup="true" />
  <div class="flow-root">
    <ul role="list" class="max-h-[258px] overflow-y-auto scroll-smooth p-6 mt-4 shadow-md rounded-lg">
      <li v-for="(activityItem, activityItemIdx) in activity" :key="activityItem.id">
        <div class="relative pb-1">
          <span v-if="activityItemIdx !== activity.length - 1"
            class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
          <div class="relative flex items-start space-x-3">
            <template v-if="activityItem.type === 'comment'">
              <div class="relative">
                <img class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-400 ring-8 ring-white"
                  :src="activityItem.imageUrl" alt="" />
                <span class="absolute -bottom-0.5 -right-1 rounded-tl bg-white px-0.5 py-px">
                  <ChatBubbleLeftEllipsisIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                </span>
              </div>
              <div class="min-w-0 flex-1">
                <div>
                  <div class="text-sm flex justify-between">
                    <a :href="activityItem.person.href" class="font-medium text-gray-900">
                      {{ activityItem.person.name }}
                    </a>
                    <div class="flex justify-end">
                      <Button @click="toggle($event, activityItem)" class="!size-4" type="button" icon="pi pi-ellipsis-v"
                        aria-haspopup="true" aria-controls="overlay_menu" text />
                    </div>
                  </div>
                  <p class="mt-0.5 text-sm text-gray-500">Comentado {{ activityItem.date }}</p>
                </div>
                <div class="mt-2 text-sm text-gray-700">
                  <p>{{ activityItem.comment }}</p>
                </div>
              </div>
            </template>
          </div>
        </div>
      </li>
    </ul>
    <CommentForm :quoteId="quoteId" class="bottom-0 left-0 right-0" />
  </div>
</template>
