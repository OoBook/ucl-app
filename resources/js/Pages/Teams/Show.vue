
<script setup>
  import { Head } from '@inertiajs/vue3';

  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

  import ItemShow from '@/Components/ItemShow.vue';

  const props = defineProps({
    pageTitle: String,
    titleField: String,
    resource: Object,
    fields: Object,
  });

</script>

<template>
  <Head :title="pageTitle ?? 'Item Details'" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ pageTitle ?? 'Item Details' }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <ItemShow
          class="bg-white rounded-lg shadow-md overflow-hidden"
          :item="resource.data"
          :fields="fields"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>
