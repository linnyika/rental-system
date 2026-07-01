package com.rentalmanagement.app.adapters;

import android.view.LayoutInflater;
import android.view.ViewGroup;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.rentalmanagement.app.databinding.ItemActivityLogBinding;
import com.rentalmanagement.app.models.ActivityLogItem;

import java.util.List;

public class ActivityLogAdapter extends RecyclerView.Adapter<ActivityLogAdapter.ViewHolder> {

    private final List<ActivityLogItem> items;

    public ActivityLogAdapter(List<ActivityLogItem> items) {
        this.items = items;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        ItemActivityLogBinding binding = ItemActivityLogBinding.inflate(
                LayoutInflater.from(parent.getContext()),
                parent,
                false
        );
        return new ViewHolder(binding);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        ActivityLogItem item = items.get(position);
        String description = item.getDescription() == null ? "-" : item.getDescription();
        String action = description;
        String task = description;

        int separatorIndex = description.indexOf(':');
        if (separatorIndex > -1 && separatorIndex < description.length() - 1) {
            action = description.substring(0, separatorIndex).trim();
            task = description.substring(separatorIndex + 1).trim();
        }

        holder.binding.tvAction.setText(action);
        holder.binding.tvTask.setText(task);
        holder.binding.tvUser.setText(item.getCaretakerId() == null ? "Caretaker: -" : "Caretaker: " + item.getCaretakerId());
        holder.binding.tvTimestamp.setText(item.getActivityDate() == null ? "-" : item.getActivityDate());
    }

    @Override
    public int getItemCount() {
        return items == null ? 0 : items.size();
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        final ItemActivityLogBinding binding;

        ViewHolder(ItemActivityLogBinding binding) {
            super(binding.getRoot());
            this.binding = binding;
        }
    }
}
