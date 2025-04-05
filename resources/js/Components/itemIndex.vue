<script setup>
  import { ref } from 'vue';

  import { Head } from '@inertiajs/vue3';

  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

  import Modal from '@/Components/Modal.vue';

  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import DangerButton from '@/Components/DangerButton.vue';

  import ItemShow from '@/Components/ItemShow.vue';
  import ItemForm from '@/Components/ItemForm.vue';

  const props = defineProps({
    pageTitle: [String],
    title: [String],
    titleField: [String],
    routePrefix: [String],
    columns: [Array],
    displayFields: [Array],
    schema: [Array],
    resource: [Array, Object],
  });

  const showFormModal = ref(false);
  const showShowModal = ref(false);

  const openFormModal = () => {
    showFormModal.value = true;
  }

  const closeFormModal = () => {
    showFormModal.value = false;
  }

  const openShowModal = () => {
    showShowModal.value = true;
  }

  const closeShowModal = () => {
    showShowModal.value = false;
  }

  const activeShowItem = ref(null);

  const showItem = (item) => {
    activeShowItem.value = item;
    openShowModal();
  }

  const activeFormItem = ref(null);
  const formProcessing = ref(false);

  const showForm = (item = null) => {
    activeFormItem.value = item;

    openFormModal();
  }

  const formSuccess = () => {
    activeFormItem.value = null;
    formProcessing.value = false;
    closeFormModal();
  }

  const formError = () => {
    formProcessing.value = false;
  }
</script>

<template>
  <Head :title="pageTitle ?? title ?? 'Items'"/>

  <AuthenticatedLayout>
    <template #header v-if="title">
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ title }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-800 text-white">
              <tr>
                <th v-for="column in columns" :key="column.key" class="px-6 py-4 text-left">
                  {{ column.title }}
                </th>
                <th class="px-6 py-4 text-left">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="item in resource.data" :key="item.id">
                <td v-for="column in columns" :key="column.key" class="px-6 py-4">
                  {{ item[column.key] ?? '' }}
                </td>
                <td class="px-6 py-4 flex gap-2">
                  <SecondaryButton @click="showItem(item)">
                    View
                  </SecondaryButton>

                  <PrimaryButton @click="showForm(item)">
                    Edit
                  </PrimaryButton>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Bottom Buttons -->
        <div class="mt-6">

          <!-- <button
            @click="generateFixtures"
            class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-md"
          >
            Generate Fixtures
          </button> -->
          <PrimaryButton @click="showFormModal = true">
            Add Item
          </PrimaryButton>
        </div>

      </div>

      <Modal :show="showFormModal" @close="closeFormModal">
        <div class="p-6">

          <ItemForm
            :routePrefix="routePrefix"
            :title="title"
            :schema="schema"
            :item="activeFormItem"

            @success="formSuccess"
            @error="formError"
            @processing="formProcessing = $event"

          >

            <template #actions="formActionsScope">
              <SecondaryButton @click="closeFormModal">
                  Cancel
              </SecondaryButton>

              <PrimaryButton
                type="submit"
                class="ms-3"
                :class="[formActionsScope.form.processing ? 'opacity-25' : '']"
                :disabled="formActionsScope.form.processing"
              >
                Save
              </PrimaryButton>
            </template>
          </ItemForm>

          <div class="mt-6 flex justify-end">
          </div>
        </div>
      </Modal>

      <Modal :show="showShowModal" @close="closeShowModal">
        <div class="p-6">
          <ItemShow
            :title="activeShowItem[titleField]"
            :item="activeShowItem"
            :fields="displayFields"
          />
          <div class="mt-6 flex justify-end">
              <SecondaryButton @click="closeShowModal">
                  Close
              </SecondaryButton>
          </div>
        </div>
      </Modal>

    </div>
  </AuthenticatedLayout>
</template>
