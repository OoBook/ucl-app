<script setup>
  import { ref, computed } from 'vue';
  import { router } from '@inertiajs/vue3';

  import { Head } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import DangerButton from '@/Components/DangerButton.vue';

  import Table from '@/Components/Table.vue';

  const props = defineProps({
    fixtures: [Array, Object],
    teams: Array
  });

  const hasFixtures = computed(() => Array.isArray(props.fixtures) ? props.fixtures.length > 0 : Object.keys(props.fixtures).length > 0);

  function generateFixtures() {
    const onError = (error) => {
      console.log('onError generateFixtures');
      console.error(error);
    }
    router.post(route('fixtures.generate'), { onError });
  }

  function clearFixtures() {
    router.post(route('fixtures.clear'));
  }
</script>

<template>
  <Head title="Fixtures" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Fixtures
      </h2>
    </template>

    <div class="py-8 mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

      <div v-if="!hasFixtures" class="mb-6">

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <Table
            :columns="[{ title: 'Teams', key: 'name' }]"
            :data="teams"
            noActions
          />
        </div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div
          v-for="(weekFixtures, week) in fixtures"
          :key="week"
          class="bg-white rounded-lg shadow-md overflow-hidden"
        >
          <div class="bg-gray-800 text-white px-4 py-3">
            <h2 class="text-xl font-bold">Week {{ week }}</h2>
          </div>
          <div class="p-4">
            <div
              v-for="fixture in weekFixtures"
              :key="fixture.id"
              class="py-3 border-b border-gray-200 last:border-0"
            >
              <div class="grid grid-cols-12">
                <span class="col-span-5">{{ fixture.home_team.name }}</span>
                <span class="col-span-2 text-center text-gray-500">
                  <template v-if="fixture.played">
                    {{ fixture.home_team_score }} - {{ fixture.away_team_score }}
                  </template>
                  <template v-else>
                    -
                  </template>
                </span>
                <span class="col-span-5 text-right">{{ fixture.away_team.name }}</span>
              </div>

              <div v-if="fixture.played" class="mt-2 text-center">
                <span class="font-bold">{{ fixture.home_team_score }} - {{ fixture.away_team_score }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <PrimaryButton
          @click="generateFixtures"
          class="mt-4"
        >
          {{ hasFixtures ? 'Regenerate Fixtures' : 'Generate Fixtures' }}
        </PrimaryButton>

        <DangerButton
          v-if="hasFixtures"
          @click="clearFixtures"

          class="ms-4 mt-4"
        >
          Clear Fixtures
        </DangerButton>
      </div>

      <!-- <div class="mt-6">
        <a
          :href="route('simulation.index')"
          class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md"
        >
          Start Simulation
        </a>
      </div> -->
    </div>
  </AuthenticatedLayout>
</template>