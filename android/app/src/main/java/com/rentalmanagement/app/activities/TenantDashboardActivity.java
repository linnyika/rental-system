package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.content.Intent;
import android.view.View;
import com.rentalmanagement.app.adapters.DashboardActionAdapter;
import com.rentalmanagement.app.adapters.DashboardListAdapter;
import com.rentalmanagement.app.adapters.DashboardMetricAdapter;
import com.rentalmanagement.app.databinding.ActivityDashboardBinding;
import com.rentalmanagement.app.models.CurrentUnitDto;
import com.rentalmanagement.app.models.TenantDashboardResponse;
import com.rentalmanagement.app.R;
import com.rentalmanagement.app.repository.DashboardRepository;
import com.rentalmanagement.app.utilities.DashboardMapper;
import java.util.Collections;
import java.util.Locale;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
public class TenantDashboardActivity extends DashboardScreenActivity {

    private DashboardRepository dashboardRepository;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityDashboardBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        dashboardRepository = new DashboardRepository();
        bindCommonHeader(displayName(), getString(R.string.tenant_dashboard));
        binding.tvStatsTitle.setText("Payment Summary");
        binding.tvStatsTitle.setVisibility(View.VISIBLE);
        binding.rvStats.setVisibility(View.VISIBLE);
        binding.tvActionsTitle.setText("Tenant Actions");
        binding.tvActionsTitle.setVisibility(View.VISIBLE);
        binding.rvActions.setVisibility(View.VISIBLE);
        binding.tvPrimaryTitle.setText("Recent Payments");
        binding.tvPrimaryTitle.setVisibility(View.VISIBLE);
        binding.rvPrimary.setVisibility(View.VISIBLE);
        binding.tvSecondaryTitle.setText("Maintenance Requests");
        binding.tvSecondaryTitle.setVisibility(View.VISIBLE);
        binding.rvSecondary.setVisibility(View.VISIBLE);
        setupGridRecycler(binding.rvStats, 1);
        setupGridRecycler(binding.rvActions, 2);
        setupListRecycler(binding.rvPrimary);
        setupListRecycler(binding.rvSecondary);
        binding.swipeRefreshLayout.setOnRefreshListener(this::loadDashboard);
        binding.btnRetry.setOnClickListener(v -> loadDashboard());
        loadDashboard();
    }

    @Override
    protected void onResume() {
        super.onResume();
        loadDashboard();
    }

    private void loadDashboard() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();
        dashboardRepository.tenantDashboard(authorization).enqueue(new Callback<TenantDashboardResponse>() {
            @Override
            public void onResponse(Call<TenantDashboardResponse> call, Response<TenantDashboardResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    showError("Failed to load dashboard. Please try again.", v -> loadDashboard());
                    return;
                }
                render(response.body());
            }

            @Override
            public void onFailure(Call<TenantDashboardResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load dashboard. Please try again.", v -> loadDashboard());
            }
        });
    }

    private void render(TenantDashboardResponse response) {
        hideError();
        binding.rvStats.setAdapter(new DashboardMetricAdapter(DashboardMapper.tenantMetrics(response.getPaymentSummary())));
        binding.rvActions.setAdapter(new DashboardActionAdapter(DashboardMapper.tenantActions(), item -> {
            if ("Request Maintenance".equalsIgnoreCase(item.getTitle())) {
                openScreen(TenantMaintenanceActivity.class);
                return;
            }
            if ("Notifications".equalsIgnoreCase(item.getTitle())) {
                openScreen(NotificationsActivity.class);
            }
        }));
        binding.rvPrimary.setAdapter(new DashboardListAdapter(DashboardMapper.recentPayments(
                response.getRecentPayments() == null ? Collections.emptyList() : response.getRecentPayments()
        )));
        binding.rvSecondary.setAdapter(new DashboardListAdapter(DashboardMapper.maintenanceRequests(
                response.getMaintenanceRequests() == null ? Collections.emptyList() : response.getMaintenanceRequests()
        )));

        CurrentUnitDto currentUnit = response.getCurrentUnit();
        binding.cardCurrentUnit.setVisibility(View.VISIBLE);
        if (currentUnit == null) {
            binding.tvCurrentUnitTitle.setText("Current Unit");
            binding.tvCurrentUnitMeta.setText("You are not currently assigned to a unit.");
            binding.tvRentStatus.setText("Pending");
            return;
        }

        binding.tvCurrentUnitTitle.setText("Unit " + safe(currentUnit.getUnitNumber()) + " - " + safe(currentUnit.getPropertyName()));
        binding.tvCurrentUnitMeta.setText("KES " + money(currentUnit.getRentAmount()));
        binding.tvRentStatus.setText(response.getRentStatus() == null ? "Pending" : capitalize(response.getRentStatus()));
    }

    private void openScreen(Class<?> targetClass) {
        Intent intent = new Intent(this, targetClass);
        intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
        startActivity(intent);
    }

    private String safe(String value) {
        return value == null || value.trim().isEmpty() ? "-" : value;
    }

    private String money(Double value) {
        return String.valueOf(Math.round(value == null ? 0 : value));
    }

    private String capitalize(String value) {
        if (value == null || value.trim().isEmpty()) {
            return "Pending";
        }
        String normalized = value.replace('_', ' ');
        return normalized.substring(0, 1).toUpperCase(Locale.ROOT) + normalized.substring(1);
    }
}
