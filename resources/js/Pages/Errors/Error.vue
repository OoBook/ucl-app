<script setup>
  import { computed } from 'vue';
  import { Head } from '@inertiajs/vue3';
  import GuestLayout from '@/Layouts/GuestLayout.vue';

  const props = defineProps({
    status: {
      type: Number,
      required: true,
    },
    message: {
      type: String,
    },
  });

  const title = computed(() => {
    return {
      403: 'Forbidden',
      404: 'Page Not Found',
      500: 'Internal Server Error',
    }[props.status] || 'An error occurred';
  });

  const description = computed(() => {
    return {
      403: 'You are not authorized to access this page.',
      404: 'The page you are looking for does not exist.',
      500: 'An internal server error occurred.',
    }[props.status] || 'An error occurred';
  });
</script>

<template>
  <Head :title="title" />

  <GuestLayout>
    <div class="flex flex-col items-center justify-center h-screen">
      <h1 class="text-2xl font-bold">
        {{ title }}
      </h1>
      <p class="text-gray-500">
        {{ message || description }}
      </p>
    </div>
  </GuestLayout>
</template>