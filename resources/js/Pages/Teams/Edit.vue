<script setup>
  import { reactive } from 'vue';
  import { router } from '@inertiajs/vue3';

  import { Head } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

  import ItemForm from '@/Components/ItemForm.vue';

  const props = defineProps({
    title: String,
    pageTitle: String,
    routePrefix: String,
    schema: Array,
    resource: Object,
  });

</script>

<template>
  <Head :title="pageTitle ?? title ?? 'Edit Item'" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ title ?? pageTitle ?? 'Edit Item' }}
      </h2>
    </template>
    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-lg">
          <ItemForm
            :routePrefix="routePrefix"
            :schema="schema"
            :item="resource.data"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
