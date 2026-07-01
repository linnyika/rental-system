package com.rentalmanagement.app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.rentalmanagement.app.databinding.ItemMaintenanceBinding;
import com.rentalmanagement.app.models.MaintenanceRequestItem;

import java.util.List;
import java.util.Locale;

public class MaintenanceAdapter extends RecyclerView.Adapter<MaintenanceAdapter.ViewHolder> {

    public interface OnMaintenanceClickListener {
        void onMaintenanceClick(MaintenanceRequestItem item);
    }

    private final List<MaintenanceRequestItem> items;
    private final boolean pendingActionEnabled;
    private final OnMaintenanceClickListener listener;

    public MaintenanceAdapter(List<MaintenanceRequestItem> items, boolean pendingActionEnabled, OnMaintenanceClickListener listener) {
        this.items = items;
        this.pendingActionEnabled = pendingActionEnabled;
        this.listener = listener;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        ItemMaintenanceBinding binding = ItemMaintenanceBinding.inflate(
                LayoutInflater.from(parent.getContext()),
                parent,
                false
        );
        return new ViewHolder(binding);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        MaintenanceRequestItem item = items.get(position);

        String unit = item.getUnit() == null || item.getUnit().getUnitNumber() == null ? "-" : item.getUnit().getUnitNumber();
        String property = item.getUnit() == null || item.getUnit().getProperty() == null || item.getUnit().getProperty().getName() == null
                ? "-"
                : item.getUnit().getProperty().getName();
        String description = item.getDescription() == null || item.getDescription().trim().isEmpty()
                ? "Maintenance request"
                : item.getDescription();

        holder.binding.tvTitle.setText(description);
        holder.binding.tvSubtitle.setText("Unit " + unit + " - " + property);
        holder.binding.tvStatus.setText(capitalize(item.getStatus()));
        holder.binding.tvMeta.setText(item.getCreatedAt() == null ? "-" : item.getCreatedAt());
        holder.binding.tvPriority.setText(item.getMajor() != null && item.getMajor() ? "Major" : "Minor");

        boolean isPending = "pending".equalsIgnoreCase(item.getStatus());
        boolean isActionable = pendingActionEnabled && isPending && listener != null;
        holder.binding.tvActionHint.setVisibility(isActionable ? View.VISIBLE : View.GONE);
        holder.binding.getRoot().setOnClickListener(v -> {
            if (isActionable) {
                listener.onMaintenanceClick(item);
            }
        });
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
        final ItemMaintenanceBinding binding;

        ViewHolder(ItemMaintenanceBinding binding) {
            super(binding.getRoot());
            this.binding = binding;
        }
    }
}
