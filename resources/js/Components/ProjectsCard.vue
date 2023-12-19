<script setup>
import { ref } from 'vue'

const items = [
  {
    id: 0,
    name: 'foo1',
    last: 'bar1',
  },
  {
    id: 1,
    name: 'foo2',
    last: 'bar2',
  },
  {
    id: 2,
    name: 'foo3',
    last: 'bar3',
  },
  {
    id: 3,
    name: 'foo4',
    last: 'bar4',
  },
  {
    id: 4,
    name: 'foo5',
    last: 'bar5',
  },
]

const getProjects = async () => {
  try {
    await axios(route('gesdoc.show'))
      .then(res => {
        datos = res.data
      })
  } catch (error) {
    console.error('No hay ná!')
  }
}
</script>

<template>
  <div class="grid grid-cols-4 gap-4 p-4">
    <div v-for="item in items" :key="item.id" class="w-40 h-40 cursor-pointer">
      <i class="fa-sharp fa-solid fa-folder fa-10x" style="color:#051869;"></i>
      {{ item.name }}
      {{ item.last }}
    </div>
  </div>
  <!-- <div class="w-20 h-20 bg-slate-300">
    <ul v-for="item in items" :key="item.id">
      <li>{{ item.name }}</li>
    </ul>
  </div> -->
</template>