<script setup>
  import { Head } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

  import ItemForm from '@/Components/ItemForm.vue';

  const props = defineProps({
    title: String,
    pageTitle: String,
    routePrefix: String,
    schema: Array,
  });

</script>

<template>
  <Head :title="pageTitle ?? title ?? 'Create Item'" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ title ?? pageTitle ?? 'Create Item' }}
      </h2>
    </template>
    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div class="">
          <ItemForm
            :routePrefix="routePrefix"
            :schema="schema"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>


</template>

