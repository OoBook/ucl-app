<script setup>
  import { ref, defineEmits } from 'vue';
  import { useForm } from '@inertiajs/vue3';

  import PrimaryButton from '@/Components/PrimaryButton.vue';

  const emit = defineEmits(['success', 'error', 'processing']);

  const props = defineProps({
    routePrefix: String,
    title: String,
    item: Object,
    schema: Array,
  });

  const processing = ref(false);

  const initialValue = props.item ?? {};

  const isEditing = initialValue.id !== undefined && initialValue.id !== null && initialValue.id !== '';

  const validSchema = props.schema.filter(field => field.name !== null && field.name !== undefined && field.name !== '');

  const fields = validSchema.map(field => field.name);

  const defaultFormValues = validSchema.reduce((acc, field) => {
    acc[field.name] = field.default ?? '';
    return acc;
  }, {});

  const formValues = fields.reduce((acc, field) => {
    acc[field] = props?.item?.[field] ?? defaultFormValues[field] ?? '';
    return acc;
  }, {});

  const form = useForm({
    ...formValues,
  });

  const submit = () => {
    processing.value = true;

    const onSuccess = () => {
      console.log('success');
      emit('processing', false);
      emit('success');
    }
    const onError = () => {
      emit('processing', false);
      emit('error');
    }

    if (isEditing) {
      emit('processing', true);
      form.put(route(`${props.routePrefix}.update`, { team: props.item.id }), {
        onSuccess,
        onError,
      });
    } else {
      emit('processing', true);
      form.post(route(`${props.routePrefix}.store`), {
        onSuccess,
        onError,
      });
    }
  }

</script>

<template>
    <h1 v-if="title" class="text-3xl font-bold mb-6">{{ title ?? 'Show Item' }}</h1>

    <form @submit.prevent="submit" class="">
      <div class="mb-4" v-for="input in validSchema" :key="input.name">
        <label for="name" class="block text-gray-700 font-bold mb-2 text-uppercase">{{ input.label ?? input.name }}</label>
        <input
          :type="input.type ?? 'text'"
          :id="input.name"
          v-model="form[input.name]"
          class="w-full px-3 py-2 border rounded-md"

          v-bind="input.attributes ?? {}"
        >
        <div v-if="form.errors[input.name]">{{ form.errors[input.name] }}</div>

      </div>

      <div class="flex justify-end">
        <slot name="actions" v-bind="{ form }">
          <PrimaryButton
            type="submit"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md"
            :disabled="form.processing"
            :class="[form.processing ? 'opacity-25' : '']"
          >
            {{ isEditing ? 'Update' : 'Create' }}
          </PrimaryButton>
        </slot>
      </div>
    </form>
</template>