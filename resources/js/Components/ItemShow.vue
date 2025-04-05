<script setup>
  import { reactive } from 'vue';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    title: String,
    item: Object,
    fields: Object,
  });

</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6" v-if="title">{{ title }}</h1>

    <template v-for="field in fields" :key="`${title}-${field.name}`">
      <div v-if="item[field.name]" class="mb-4 flex text-uppercase">
        <p class="text-gray-700 font-bold mr-2">{{ field.label }}:</p>
        <p class="text-gray-700">{{ item[field.name] }}</p>
      </div>
    </template>

  </div>
</template>