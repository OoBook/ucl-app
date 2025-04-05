<script setup>
import ItemIndex from '@/Components/itemIndex.vue';

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


// function generateFixtures() {
//   router.post(route('fixtures.generate'));
// }

// function showTeamDetails(team) {
//   router.get(route('teams.show', { team: team.id }));
// }

</script>

<template>
  <ItemIndex
    :pageTitle="pageTitle"
    :title="title"
    :titleField="titleField"
    :routePrefix="routePrefix"
    :columns="columns"
    :displayFields="displayFields"
    :schema="schema"
    :resource="resource"
  />
</template>