package com.rentalmanagement.app.adapters;

import android.view.LayoutInflater;
import android.view.ViewGroup;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.rentalmanagement.app.databinding.ItemDashboardListBinding;
import com.rentalmanagement.app.models.DashboardListItem;

import java.util.List;

public class DashboardListAdapter extends RecyclerView.Adapter<DashboardListAdapter.ViewHolder> {

    public interface OnItemClickListener {
        void onItemClick(DashboardListItem item, int position);
    }

    private final List<DashboardListItem> items;
    private final OnItemClickListener listener;

    public DashboardListAdapter(List<DashboardListItem> items) {
        this(items, null);
    }

    public DashboardListAdapter(List<DashboardListItem> items, OnItemClickListener listener) {
        this.items = items;
        this.listener = listener;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        ItemDashboardListBinding binding = ItemDashboardListBinding.inflate(
                LayoutInflater.from(parent.getContext()),
                parent,
                false
        );
        return new ViewHolder(binding);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        DashboardListItem item = items.get(position);
        holder.binding.tvTitle.setText(item.getTitle());
        holder.binding.tvSubtitle.setText(item.getSubtitle());
        holder.binding.tvStatus.setText(item.getStatus());
        holder.binding.tvMeta.setText(item.getMeta());
        holder.binding.getRoot().setOnClickListener(v -> {
            if (listener != null) {
                listener.onItemClick(item, position);
            }
        });
    }

    @Override
    public int getItemCount() {
        return items.size();
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        final ItemDashboardListBinding binding;

        ViewHolder(ItemDashboardListBinding binding) {
            super(binding.getRoot());
            this.binding = binding;
        }
    }
}
