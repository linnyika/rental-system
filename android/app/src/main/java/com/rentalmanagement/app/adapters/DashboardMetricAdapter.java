package com.rentalmanagement.app.adapters;

import android.view.LayoutInflater;
import android.view.ViewGroup;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.rentalmanagement.app.databinding.ItemDashboardMetricBinding;
import com.rentalmanagement.app.models.DashboardMetricItem;

import java.util.List;

public class DashboardMetricAdapter extends RecyclerView.Adapter<DashboardMetricAdapter.ViewHolder> {

    private final List<DashboardMetricItem> items;

    public DashboardMetricAdapter(List<DashboardMetricItem> items) {
        this.items = items;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        ItemDashboardMetricBinding binding = ItemDashboardMetricBinding.inflate(
                LayoutInflater.from(parent.getContext()),
                parent,
                false
        );
        return new ViewHolder(binding);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        DashboardMetricItem item = items.get(position);
        holder.binding.tvLabel.setText(item.getLabel());
        holder.binding.tvValue.setText(item.getValue());
        holder.binding.tvIcon.setText(item.getIcon());
    }

    @Override
    public int getItemCount() {
        return items.size();
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        final ItemDashboardMetricBinding binding;

        ViewHolder(ItemDashboardMetricBinding binding) {
            super(binding.getRoot());
            this.binding = binding;
        }
    }
}
