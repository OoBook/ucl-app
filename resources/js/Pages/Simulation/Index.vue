<script setup>
  import { computed } from 'vue';
  import { router } from '@inertiajs/vue3';

  import { Head } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

  const props = defineProps({
    teams: Array,
    currentWeek: Number,
    fixtures: Array,
    // predictions: Array,
    totalWeeks: Number
  });

  console.log(props.teams, props.fixtures);

  const sortedTable = computed(() => {
    return [...props.teams].sort((a, b) => {
      // Sort by points (descending)
      if (a.points !== b.points) {
        return b.points - a.points;
      }

      // If points are equal, sort by goal difference
      if (a.goal_difference !== b.goal_difference) {
        return b.goal_difference - a.goal_difference;
      }

      // If goal difference is equal, sort by goals for
      return b.goals_for - a.goals_for;
    });
  });

  function playNextWeek() {
    router.post(route('simulation.playNextWeek'));
  }

  function playAllWeeks() {
    router.post(route('simulation.playAllWeeks'));
  }

  function resetData() {
    router.post(route('simulation.reset'));
  }
</script>

<template>
  <Head title="Simulation" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Simulation
      </h2>
    </template>
    <div class="container mx-auto px-4 py-8">
      <!-- <h1 class="text-3xl font-bold mb-6">Simulation</h1> -->

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- League Table -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
              <thead class="bg-gray-800 text-white">
                <tr>
                  <th class="px-4 py-3 text-left">Team Name</th>
                  <th class="px-4 py-3 text-center">Pts</th>
                  <th class="px-3 py-3 text-center">P</th>
                  <th class="px-3 py-3 text-center">W</th>
                  <th class="px-3 py-3 text-center">D</th>
                  <th class="px-3 py-3 text-center">L</th>
                  <th class="px-3 py-3 text-center">GD</th>
                  <th class="px-3 py-3 text-center">GF</th>
                  <th class="px-3 py-3 text-center">GA</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="table in sortedTable" :key="table.id">
                  <td class="px-4 py-3">{{ table.team.name }}</td>
                  <td class="px-3 py-3 text-center">{{ table.points }}</td>
                  <td class="px-3 py-3 text-center">{{ table.played }}</td>
                  <td class="px-3 py-3 text-center">{{ table.won }}</td>
                  <td class="px-3 py-3 text-center">{{ table.drawn }}</td>
                  <td class="px-3 py-3 text-center">{{ table.lost }}</td>
                  <td class="px-3 py-3 text-center">{{ table.goal_difference }}</td>
                  <td class="px-3 py-3 text-center">{{ table.goals_for }}</td>
                  <td class="px-3 py-3 text-center">{{ table.goals_against }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Current Week Matches -->
        <div>
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-800 text-white px-4 py-3">
              <h2 class="text-xl font-bold">Week {{ currentWeek }}</h2>
            </div>
            <div class="p-4">
              <div
                v-for="fixture in fixtures"
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

          <!-- Championship Predictions -->
          <!-- <div class="mt-6 bg-white rounded-lg shadow-md overflow-hidden" v-if="currentWeek >= 4 && predictions.length > 0">
            <div class="bg-gray-800 text-white px-4 py-3">
              <h2 class="text-xl font-bold">Championship Predictions</h2>
            </div>
            <div class="p-4">
              <div
                v-for="prediction in predictions"
                :key="prediction.id"
                class="py-2 flex justify-between items-center"
              >
                <span>{{ prediction.team.name }}</span>
                <span class="font-bold">{{ prediction.win_probability }}%</span>
              </div>
            </div>
          </div> -->
        </div>
      </div>

      <!-- Controls -->
      <div class="mt-8 flex flex-wrap gap-4">
        <button
          @click="playNextWeek"
          class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-md"
          :disabled="currentWeek >= totalWeeks"
          :class="[currentWeek >= totalWeeks ? 'opacity-50' : '']"
        >
          Play Next Week
        </button>

        <button
          @click="playAllWeeks"
          class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-md"
          :disabled="currentWeek >= totalWeeks"
          :class="[currentWeek >= totalWeeks ? 'opacity-50' : '']"
        >
          Play All Weeks
        </button>

        <button
          @click="resetData"
          class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-md"
          :disabled="currentWeek == 1"
          :class="[currentWeek == 1 ? 'opacity-50' : '']"
        >
          Reset Data
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
