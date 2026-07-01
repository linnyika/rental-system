package com.rentalmanagement.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import com.rentalmanagement.app.adapters.DashboardActionAdapter;
import com.rentalmanagement.app.adapters.DashboardListAdapter;
import com.rentalmanagement.app.adapters.DashboardMetricAdapter;
import com.rentalmanagement.app.databinding.ActivityDashboardBinding;
import com.rentalmanagement.app.models.LandlordDashboardResponse;
import com.rentalmanagement.app.R;
import com.rentalmanagement.app.repository.DashboardRepository;
import com.rentalmanagement.app.utilities.DashboardMapper;
import java.util.Collections;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
public class LandlordDashboardActivity extends DashboardScreenActivity {

    private DashboardRepository dashboardRepository;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityDashboardBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        dashboardRepository = new DashboardRepository();
        bindCommonHeader(displayName(), getString(R.string.landlord_dashboard));
        binding.cardCurrentUnit.setVisibility(View.GONE);
        binding.tvStatsTitle.setText("Overview");
        binding.tvStatsTitle.setVisibility(View.VISIBLE);
        binding.rvStats.setVisibility(View.VISIBLE);
        binding.tvActionsTitle.setText("Management Actions");
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
        dashboardRepository.landlordDashboard(authorization).enqueue(new Callback<LandlordDashboardResponse>() {
            @Override
            public void onResponse(Call<LandlordDashboardResponse> call, Response<LandlordDashboardResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    showError("Failed to load dashboard. Please try again.", v -> loadDashboard());
                    return;
                }
                render(response.body());
            }

            @Override
            public void onFailure(Call<LandlordDashboardResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load dashboard. Please try again.", v -> loadDashboard());
            }
        });
    }

    private void render(LandlordDashboardResponse response) {
        hideError();
        binding.rvStats.setAdapter(new DashboardMetricAdapter(DashboardMapper.landlordMetrics(response.getStats())));
        binding.rvActions.setAdapter(new DashboardActionAdapter(DashboardMapper.landlordActions(), item -> {
            if ("Properties".equalsIgnoreCase(item.getTitle())) {
                openScreen(PropertiesActivity.class);
                return;
            }
            if ("Units".equalsIgnoreCase(item.getTitle())) {
                openScreen(UnitsActivity.class);
                return;
            }
            if ("Register Tenant".equalsIgnoreCase(item.getTitle())) {
                openScreen(RegisterTenantActivity.class);
                return;
            }
            if ("Register Caretaker".equalsIgnoreCase(item.getTitle())) {
                openScreen(RegisterCaretakerActivity.class);
                return;
            }
            if ("Maintenance".equalsIgnoreCase(item.getTitle())) {
                openScreen(LandlordMaintenanceActivity.class);
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
    }

    private void openScreen(Class<?> targetClass) {
        Intent intent = new Intent(this, targetClass);
        intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
        startActivity(intent);
    }
}
