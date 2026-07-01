package com.rentalmanagement.app.adapters;

import android.view.LayoutInflater;
import android.view.ViewGroup;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.rentalmanagement.app.databinding.ItemDashboardActionBinding;
import com.rentalmanagement.app.models.DashboardActionItem;

import java.util.List;

public class DashboardActionAdapter extends RecyclerView.Adapter<DashboardActionAdapter.ViewHolder> {

    public interface OnActionClickListener {
        void onActionClick(DashboardActionItem item);
    }

    private final List<DashboardActionItem> items;
    private final OnActionClickListener listener;

    public DashboardActionAdapter(List<DashboardActionItem> items, OnActionClickListener listener) {
        this.items = items;
        this.listener = listener;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        ItemDashboardActionBinding binding = ItemDashboardActionBinding.inflate(
                LayoutInflater.from(parent.getContext()),
                parent,
                false
        );
        return new ViewHolder(binding);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        DashboardActionItem item = items.get(position);
        holder.binding.tvTitle.setText(item.getTitle());
        holder.binding.tvSubtitle.setText(item.getSubtitle());
        holder.binding.tvIcon.setText(item.getIcon());
        holder.binding.getRoot().setOnClickListener(v -> listener.onActionClick(item));
    }

    @Override
    public int getItemCount() {
        return items.size();
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        final ItemDashboardActionBinding binding;

        ViewHolder(ItemDashboardActionBinding binding) {
            super(binding.getRoot());
            this.binding = binding;
        }
    }
}
