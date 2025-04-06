<script setup>
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import DangerButton from '@/Components/DangerButton.vue';

  const emit = defineEmits(['show', 'edit', 'delete']);

  const props = defineProps({
    columns: Array,
    data: Array,
    noActions: Boolean,
  });

  const show = (item) => {
    emit('show', item);
  }

  const edit = (item) => {
    emit('edit', item);
  }

  const remove = (item) => {
    emit('remove', item);
  }

</script>

<template>
  <!-- Table -->
  <table class="w-full">
    <thead class="bg-gray-800 text-white">
      <tr>
        <th v-for="column in columns" :key="column.key" class="px-6 py-4 text-left">
          {{ column.title }}
        </th>
        <th v-if="!noActions" class="px-6 py-4 text-left">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
      <tr v-for="item in data" :key="item.id">
        <td v-for="column in columns" :key="column.key" class="px-6 py-4">
          {{ item[column.key] ?? '' }}
        </td>
        <td v-if="!noActions" class="px-6 py-4 flex gap-2">
          <SecondaryButton @click="show(item)">
            View
          </SecondaryButton>

          <PrimaryButton @click="edit(item)">
            Edit
          </PrimaryButton>

          <DangerButton @click="remove(item)">
            Remove
          </DangerButton>
        </td>
      </tr>
    </tbody>
  </table>
</template>
