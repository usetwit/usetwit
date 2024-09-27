<script setup>
import { computed } from 'vue'
import { useDropdown } from '../../composables/useDropdown.js'
import Checkbox from '../Form/Checkbox.vue'

const columns = defineModel()

const visible = computed(() => {
    return columns.value.filter(col => col.visible === true).sort((a, b) => a.label.localeCompare(b.label))
})

const sorted = computed(() => {
    return columns.value.sort((a, b) => a.label.localeCompare(b.label))
})

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown('left', 'bottom')
</script>

<template>
    <div class="pb-4 overflow-x-auto">
        <fieldset class="flex border border-gray-200 text-gray-700 rounded-lg p-0.5 hover:cursor-pointer"
                  ref="inputRef"
                  @click.self="toggleDropdown"
        >
            <button type="button"
                    ref="buttonRef"
                    class="pl-2.5 pr-2 py-1.5 mx-0.5 text-sm inline-flex items-center bg-slate-800 text-white rounded-lg text-nowrap hover:bg-slate-700"
                    @click.self="toggleDropdown"
            >
                <i class="pi pi-bars mr-2"></i>Columns
            </button>
            <Teleport to="body">
                <div v-if="showDropdown"
                     ref="dropdownRef"
                     class="rounded absolute z-50 bg-white shadow border-gray-200 border flex flex-col overflow-y-auto p-1 min-w-64"
                     :style="dropdownStyle"
                >
                    <ul>
                        <li v-for="col in sorted" :key="col.field" class="text-sm">
                            <Checkbox :label="col.label"
                                      :id="col.field"
                                      v-model="col.visible"
                                      class="w-full px-3 py-1.5"
                            />
                        </li>
                    </ul>
                </div>
            </Teleport>

            <span v-for="col in visible"
                  :key="col.field"
                  class="hover:cursor-auto select-none pl-2.5 pr-2 py-0.5 mx-0.5 text-sm inline-flex items-center bg-gray-200 text-gray-800 rounded-lg text-nowrap"
                  @click="showDropdown = false"
            >
                {{ col.label }}
                <button type="button"
                        @click="col.visible = false"
                        class="inline-flex align-middle ml-0.5 p-1 text-sm hover:bg-gray-100 rounded-full"
                >
                <i class="pi pi-times-circle"></i>
            </button>
        </span>
        </fieldset>
    </div>
</template>
