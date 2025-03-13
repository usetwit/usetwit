<script setup>
import csrfToken from '@/csrf.js';
</script>

<template>
<input type="hidden" name="_token" :value="csrfToken"/>
</template>
