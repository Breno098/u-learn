<script setup>
    import { ref, onMounted, reactive } from 'vue'

    const props = defineProps({
        image: String
    });

    const elements = reactive([{
        x: 500,
        y: 100,
        width: 50,
        height: 50,
        content: '<div style="background: red; width: 100%; height: 100%"> LOGO </div>',
        index: 1
    }, {
        x: 900,
        y: 100,
        width: 200,
        height: 100,
        content: `
            <div style="border: 1px dashed red; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center">
                ICON
            </div>
        `,
        index: 1
    }])

    const isClick = ref(false)

    const activeElementIndex = ref(null)

    const handleMouseMove = (event) => {
        if (isClick.value) {
            var x = event.clientX - clickX.value - 300;
            var y = event.clientY - clickY.value - 500;

            elements[activeElementIndex.value].x = x;
            elements[activeElementIndex.value].y = y;
        }
    }

    const handleMouseDown = (event, index) => {
        isClick.value = true;
        activeElementIndex.value = index;
        clickX.value = event.layerX;
        clickY.value = event.layerY;
    }

    const handleMouseUp = () => {
        isClick.value = false;
    }

    const clickX = ref(0);
    const clickY = ref(0);

    const handleClickEvent = (event, index) => {
        isClick.value = !isClick.value;
        activeElementIndex.value = index;
        clickX.value = event.layerX;
        clickY.value = event.layerY;
    }
</script>

<template>
    <pre>
        {{ elements }}
    </pre>
    <div
        class="bg-grey-4"
        id="total-area"
        :style="{
            width: '90%',
            height: '850px',
            border: '1px dashed red',
            position: 'absolute',
        }"
        style="z-index: 1;"
        @mousemove="handleMouseMove"
        @mouseup="handleMouseUp"
    >
            <div
                v-for="(element, index) in elements"
                :style="{
                    width: `${element.width}px`,
                    height: `${element.height}px`,
                    top: `${element.y}px`,
                    left: `${element.x}px`,
                    position: 'absolute',
                    border: index === activeElementIndex? '1px dashed black' : '',
                    cursor: 'pointer',
                }"
                style="z-index: -1 !important;"
                v-html="element.content"
                @mousedown="handleMouseDown($event, index)"
            >

            </div>
    </div>
</template>

