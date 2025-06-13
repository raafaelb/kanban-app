import './bootstrap';

import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

window.Alpine = Alpine;

Alpine.start();


document.querySelectorAll('.tasks-container').forEach(container => {
    new Sortable(container, {
      group: 'tasks',
      animation: 150,
      onEnd: (evt) => {
        const taskId = evt.item.dataset.taskId;
        const newColumnId = evt.to.closest('[data-column-id]').dataset.columnId;
        const newOrder = Array.from(evt.to.children).indexOf(evt.item);

        $.ajax({
          url: "{{ route('tasks.move') }}",
          method: 'POST',
          data: {
            _token: "{{ csrf_token() }}",
            task_id: taskId,
            new_column_id: newColumnId,
            new_order: newOrder
          }
        });
      }
    });
  });
