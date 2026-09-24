<!-- Task Collaboration & Discussion Modal -->
<div class="modal" id="task-comments-modal">
    <div class="modal-dialog" style="max-width: 640px;">
        <div class="modal-header">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 700;" id="comments-modal-title">Task Discussion</h3>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;" id="comments-modal-meta">
                    Loading details...
                </div>
            </div>
            <button type="button" onclick="closeModal('task-comments-modal')" style="background:none; border:none; color:var(--text-muted); font-size:1.4rem; cursor:pointer;" aria-label="Close dialog">&times;</button>
        </div>
        <div class="modal-body" style="padding: 1.25rem;">
            <!-- Discussion Thread -->
            <div id="comments-thread" style="display: flex; flex-direction: column; gap: 0.85rem; max-height: 360px; overflow-y: auto; margin-bottom: 1.25rem; padding-right: 0.35rem;">
                <div style="text-align: center; color: var(--text-dim); padding: 2rem;">
                    Loading conversation...
                </div>
            </div>

            <!-- New Comment Box -->
            <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                <form id="add-comment-form" method="POST" onsubmit="handleCommentSubmit(event)">
                    @csrf
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" for="task-comment-input">Post Note / Update</label>
                        <textarea id="task-comment-input" name="comment" class="form-control" rows="3" required placeholder="Write instructions, progress report, or status blocker..."></textarea>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.725rem; color: var(--text-dim);">
                            🔔 Automatic alerts dispatched to assignees
                        </span>
                        <button type="submit" id="btn-submit-comment" class="btn btn-primary btn-sm">
                            <span>Post Note 💬</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let activeCommentTaskId = null;

    function openTaskCommentsModal(taskId, taskTitle) {
        activeCommentTaskId = taskId;
        const modal = document.getElementById('task-comments-modal');
        const titleEl = document.getElementById('comments-modal-title');
        const metaEl = document.getElementById('comments-modal-meta');
        const threadEl = document.getElementById('comments-thread');
        const formEl = document.getElementById('add-comment-form');
        const inputEl = document.getElementById('task-comment-input');

        titleEl.textContent = '💬 ' + taskTitle;
        metaEl.textContent = 'Task #' + taskId + ' Discussion Thread';
        formEl.action = '/tasks/' + taskId + '/comments';
        inputEl.value = '';

        threadEl.innerHTML = `
            <div style="text-align: center; color: var(--text-dim); padding: 2.5rem 0;">
                <div style="display: inline-block; animation: spin 1s linear infinite; font-size: 1.5rem; margin-bottom: 0.5rem;">⏳</div>
                <div>Loading notes & updates...</div>
            </div>
        `;

        openModal('task-comments-modal');

        fetch(`/tasks/${taskId}/comments`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to load notes');
            return res.json();
        })
        .then(data => {
            metaEl.textContent = `Task #${data.task.id} • ${data.task.status.toUpperCase()} • Assignee: ${data.task.assigned_to_name}`;
            renderCommentsThread(data.comments);
        })
        .catch(err => {
            threadEl.innerHTML = `
                <div style="text-align: center; color: #fb7185; padding: 2rem;">
                    Failed to load discussion. Please check permissions or refresh.
                </div>
            `;
        });
    }

    function renderCommentsThread(comments) {
        const threadEl = document.getElementById('comments-thread');
        if (!comments || comments.length === 0) {
            threadEl.innerHTML = `
                <div style="text-align: center; color: var(--text-dim); padding: 2.5rem 1rem;">
                    <div style="font-size: 1.75rem; margin-bottom: 0.4rem;">💬</div>
                    <div style="font-weight: 600;">No team notes yet</div>
                    <div style="font-size: 0.775rem; margin-top: 0.2rem;">Be the first to leave an update or instruction for this task.</div>
                </div>
            `;
            return;
        }

        let html = '';
        comments.forEach(c => {
            const roleClass = c.user.role === 'admin' ? 'portal-admin' : (c.user.role === 'manager' ? 'portal-manager' : 'portal-employee');
            html += `
                <div style="display: flex; gap: 0.75rem; background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 0.85rem;">
                    <div style="width: 34px; height: 34px; border-radius: var(--radius-sm); background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; color: #fff; flex-shrink: 0;">
                        ${c.user.initials}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);">${escapeHtml(c.user.name)}</span>
                                <span class="role-portal-tag ${roleClass}" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">${c.user.role}</span>
                            </div>
                            <span style="font-size: 0.72rem; color: var(--text-dim);">${c.created_at}</span>
                        </div>
                        <div style="font-size: 0.835rem; color: var(--text-muted); line-height: 1.45; white-space: pre-wrap; word-break: break-word;">${escapeHtml(c.comment)}</div>
                    </div>
                </div>
            `;
        });

        threadEl.innerHTML = html;
        threadEl.scrollTop = threadEl.scrollHeight;
    }

    function handleCommentSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btn-submit-comment');
        const input = document.getElementById('task-comment-input');
        const comment = input.value.trim();

        if (!comment) return;

        btn.disabled = true;
        btn.innerHTML = '<span>Posting...</span>';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ comment: comment })
        })
        .then(res => {
            if (!res.ok) throw new Error('Submission failed');
            return res.json();
        })
        .then(data => {
            input.value = '';
            btn.disabled = false;
            btn.innerHTML = '<span>Post Note 💬</span>';

            // Refresh discussion thread
            fetch(`/tasks/${activeCommentTaskId}/comments`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(resData => {
                renderCommentsThread(resData.comments);
                // Update badge in task table if present
                const badgeEl = document.getElementById(`task-comment-count-${activeCommentTaskId}`);
                if (badgeEl) {
                    badgeEl.textContent = resData.comments.length;
                }
            });
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span>Post Note 💬</span>';
            // Fallback to normal form submit if fetch fails
            form.submit();
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
