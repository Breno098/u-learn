<script setup>
    import { ref, onMounted, reactive } from 'vue'

    const props = defineProps({
        position: {
            type: Object,
            default: () => ({ x: 0, y: 0 })
        },
        image: String
    });

    const elements = reactive([
        {
            // type: 'rectangle',
            // x: 10,
            // y: 10,
            // width: 50,
            // height: 50
        }
    ])

    const isDragging = ref(false)

    const dragStart = ref({ x: 0, y: 0 })

    const canvas = ref(null);

    const styleObject = reactive({
        color: 'red',
        fontSize: '13px',
        width: '1000px',
        height: '500px',
        border: '1px dashed black',
    })

    const draw = () => {
        const context = canvas.value.getContext('2d');

        context.clearRect(0, 0, canvas.value.width, canvas.value.height);

        elements.forEach(element => {
            if (element.type === 'rectangle') {
                element.path.rect(element.x, element.y, element.width, element.height);
                context.fill(element.path);
            }
        });
    }

    const handleMouseDown = (event) => {
        dragStart.value = { x: event.offsetX, y: event.offsetY };
    }

    const handleMouseMove = (event) => {
        if (isDragging.value) {
            const dragEnd = { x: event.offsetX, y: event.offsetY };
            const diff = { x: dragEnd.x - dragStart.value.x, y: dragEnd.y - dragStart.value.y };
            elements[0].x += diff.x;
            elements[0].y += diff.y;
            dragStart.value = dragEnd;
            draw();
        }
    }

    const handleMouseOver = (event) => {
        if (! isDragging.value) {
            const context = canvas.value.getContext('2d');

            const rect = canvas.value.getBoundingClientRect();
            const elementRelativeX = event.clientX - rect.left;
            const elementRelativeY = event.clientY - rect.top;
            const x = elementRelativeX * canvas.value.width / rect.width;
            const y = elementRelativeY * canvas.value.height / rect.height;

            elements.forEach(element => {
                if (context.isPointInPath(element.path, x, y)) {
                    context.fillStyle = 'green';
                    context.fill(element.path);
                }
                else {
                    context.fillStyle = 'red';
                    context.fill(element.path);
                }
            });
        }
    };

    const handleMouseUp = () => {
        isDragging.value = false;
    }

    const drawRect = (event) => {
        if (isDragging.value) {
            const rect = canvas.value.getBoundingClientRect();
            const elementRelativeX = event.clientX - rect.left;
            const elementRelativeY = event.clientY - rect.top;
            const x = elementRelativeX * canvas.value.width / rect.width;
            const y = elementRelativeY * canvas.value.height / rect.height;

            const path = new Path2D();

            elements.push({
                type: 'rectangle',
                x: x,
                y: y,
                width: 50,
                height: 50,
                path: path
            })

            draw();
        }
    }
</script>

<template>
    <!-- <img id="scream" :src="image" style="display: none;"> -->

    <!-- @mousedown="handleMouseDown"
        @mousemove="handleMouseMove"
        @mouseup="handleMouseUp" -->
    <pre>
        {{ elements }}
    </pre>
    <div class="bg-grey-4">
        <canvas
            ref="canvas"
            :style="styleObject"
            @click="drawRect"
            @mousedown="handleMouseOver"
        ></canvas>
    </div>
    <div class="bg-grey-3">
        <q-btn
            color="negative"
            class="q-mr-md"
            no-caps
            outline
            @click="isDragging = !isDragging"
            label="!isDragging"
        />

        {{ !isDragging ? 'Clique e desenhe' : 'Desenho não habilitad'  }}
    </div>
</template>

