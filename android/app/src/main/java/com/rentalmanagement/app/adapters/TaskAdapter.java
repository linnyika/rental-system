package com.rentalmanagement.app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.rentalmanagement.app.databinding.ItemTaskBinding;
import com.rentalmanagement.app.models.TaskItem;

import java.util.List;
import java.util.Locale;

public class TaskAdapter extends RecyclerView.Adapter<TaskAdapter.ViewHolder> {

    public enum Mode {
        CARETAKER,
        TENANT_CONFIRM
    }

    public enum TaskAction {
        START,
        COMPLETE,
        CONFIRM
    }

    public interface OnTaskActionClickListener {
        void onTaskActionClick(TaskItem item, TaskAction action);
    }

    private final List<TaskItem> items;
    private final Mode mode;
    private final OnTaskActionClickListener listener;

    public TaskAdapter(List<TaskItem> items, Mode mode, OnTaskActionClickListener listener) {
        this.items = items;
        this.mode = mode;
        this.listener = listener;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        ItemTaskBinding binding = ItemTaskBinding.inflate(
                LayoutInflater.from(parent.getContext()),
                parent,
                false
        );
        return new ViewHolder(binding);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        TaskItem item = items.get(position);

        String description = item.getRequest() == null || item.getRequest().getDescription() == null
                ? "Maintenance task"
                : item.getRequest().getDescription();
        String unit = item.getRequest() == null || item.getRequest().getUnit() == null || item.getRequest().getUnit().getUnitNumber() == null
                ? "-"
                : item.getRequest().getUnit().getUnitNumber();
        String property = item.getRequest() == null || item.getRequest().getUnit() == null || item.getRequest().getUnit().getProperty() == null || item.getRequest().getUnit().getProperty().getName() == null
                ? "-"
                : item.getRequest().getUnit().getProperty().getName();

        holder.binding.tvTitle.setText(description);
        holder.binding.tvSubtitle.setText("Unit " + unit + " - " + property);
        holder.binding.tvStatus.setText(capitalize(item.getStatus()));

        String meta = item.getCompletedAt() != null ? item.getCompletedAt() : item.getCreatedAt();
        holder.binding.tvMeta.setText(meta == null ? "-" : meta);

        if (mode == Mode.CARETAKER) {
            bindCaretakerToggle(holder, item);
            return;
        }

        holder.binding.taskStatusToggleGroup.setVisibility(View.GONE);
        TaskAction action = resolveAction(item);
        boolean actionable = action != null && listener != null;
        holder.binding.tvActionHint.setVisibility(actionable ? View.VISIBLE : View.GONE);
        holder.binding.tvActionHint.setText(actionHint(action));
        holder.binding.getRoot().setOnClickListener(v -> {
            if (actionable) {
                listener.onTaskActionClick(item, action);
            }
        });
    }

    private void bindCaretakerToggle(ViewHolder holder, TaskItem item) {
        holder.binding.getRoot().setOnClickListener(null);
        holder.binding.tvActionHint.setVisibility(View.GONE);
        holder.binding.taskStatusToggleGroup.setVisibility(View.VISIBLE);

        String status = item.getStatus() == null ? "" : item.getStatus().toLowerCase(Locale.ROOT);
        int checkedButtonId;
        if ("assigned".equals(status)) {
            checkedButtonId = holder.binding.btnStatusStart.getId();
        } else if ("in_progress".equals(status)) {
            checkedButtonId = holder.binding.btnStatusInProgress.getId();
        } else {
            checkedButtonId = holder.binding.btnStatusComplete.getId();
        }

        holder.binding.taskStatusToggleGroup.check(checkedButtonId);
        holder.binding.btnStatusStart.setEnabled("assigned".equals(status));
        holder.binding.btnStatusInProgress.setEnabled(false);
        holder.binding.btnStatusComplete.setEnabled("in_progress".equals(status));

        holder.binding.btnStatusStart.setOnClickListener(v -> {
            if (listener != null && "assigned".equals(status)) {
                listener.onTaskActionClick(item, TaskAction.START);
            }
        });

        holder.binding.btnStatusComplete.setOnClickListener(v -> {
            if (listener != null && "in_progress".equals(status)) {
                listener.onTaskActionClick(item, TaskAction.COMPLETE);
            }
        });
    }

    private TaskAction resolveAction(TaskItem item) {
        if (mode == Mode.TENANT_CONFIRM) {
            return "done".equalsIgnoreCase(item.getStatus()) ? TaskAction.CONFIRM : null;
        }

        if ("assigned".equalsIgnoreCase(item.getStatus())) {
            return TaskAction.START;
        }
        if ("in_progress".equalsIgnoreCase(item.getStatus())) {
            return TaskAction.COMPLETE;
        }
        return null;
    }

    private String actionHint(TaskAction action) {
        if (action == TaskAction.START) {
            return "Tap to start";
        }
        if (action == TaskAction.COMPLETE) {
            return "Tap to complete";
        }
        if (action == TaskAction.CONFIRM) {
            return "Tap to confirm";
        }
        return "";
    }

    @Override
    public int getItemCount() {
        return items == null ? 0 : items.size();
    }

    private String capitalize(String value) {
        if (value == null || value.trim().isEmpty()) {
            return "-";
        }
        String normalized = value.replace('_', ' ');
        return normalized.substring(0, 1).toUpperCase(Locale.ROOT) + normalized.substring(1);
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        final ItemTaskBinding binding;

        ViewHolder(ItemTaskBinding binding) {
            super(binding.getRoot());
            this.binding = binding;
        }
    }
}
