package com.rentalmanagement.app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.rentalmanagement.app.databinding.ItemNotificationBinding;
import com.rentalmanagement.app.models.NotificationItem;

import java.util.List;

public class NotificationAdapter extends RecyclerView.Adapter<NotificationAdapter.ViewHolder> {

    public interface OnNotificationClickListener {
        void onNotificationClick(NotificationItem item);
    }

    private final List<NotificationItem> items;
    private final OnNotificationClickListener listener;

    public NotificationAdapter(List<NotificationItem> items, OnNotificationClickListener listener) {
        this.items = items;
        this.listener = listener;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        ItemNotificationBinding binding = ItemNotificationBinding.inflate(
                LayoutInflater.from(parent.getContext()),
                parent,
                false
        );
        return new ViewHolder(binding);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        NotificationItem item = items.get(position);
        holder.binding.tvTitle.setText(item.getTitle() == null ? "-" : item.getTitle());
        holder.binding.tvMessage.setText(item.getMessage() == null ? "-" : item.getMessage());
        holder.binding.tvType.setText(item.getType() == null ? "-" : item.getType());
        holder.binding.tvTimestamp.setText(item.getCreatedAt() == null ? "-" : item.getCreatedAt());
        holder.binding.tvStatus.setText(item.isRead() ? "Read" : "Unread");

        boolean canMarkRead = !item.isRead() && listener != null;
        holder.binding.tvActionHint.setVisibility(canMarkRead ? View.VISIBLE : View.GONE);
        holder.binding.getRoot().setOnClickListener(v -> {
            if (canMarkRead) {
                listener.onNotificationClick(item);
            }
        });
    }

    @Override
    public int getItemCount() {
        return items == null ? 0 : items.size();
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        final ItemNotificationBinding binding;

        ViewHolder(ItemNotificationBinding binding) {
            super(binding.getRoot());
            this.binding = binding;
        }
    }
}
