<template>
  <div v-form>
    <Field
      uicontrol="multituple"
      name="multitupletextvalue"
      title="Multiple values with values (two)"
      inline-help="Multi Tuple again."
      :ui-control-attributes="{
        field1: {
          key: 'index',
          title: 'Index',
          uiControl: 'text',
          availableValues: null,
        },
        field2: {
          key: 'value',
          title: 'Value',
          uiControl: 'text',
          availableValues: null,
        },
      }"
      v-model="values"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { Field, Form } from 'CorePluginsAdmin';

export default defineComponent({
  components: {
    Field,
  },
  directives: {
    Form,
  },
  data() {
    return {
      values: [
        { index: 'test', value: 'myfoo' },
        { index: 'test 2', value: 'myfoo 2' },
      ],
    };
  },
});
</script>
