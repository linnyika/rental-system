package com.rentalmanagement.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;
import com.rentalmanagement.app.adapters.DashboardActionAdapter;
import androidx.recyclerview.widget.RecyclerView;
import com.rentalmanagement.app.adapters.DashboardListAdapter;
import com.rentalmanagement.app.adapters.DashboardMetricAdapter;
import com.rentalmanagement.app.databinding.ActivityDashboardBinding;
import com.rentalmanagement.app.models.AdminDashboardResponse;
import com.rentalmanagement.app.models.DashboardListItem;
import com.rentalmanagement.app.R;
import com.rentalmanagement.app.repository.DashboardRepository;
import com.rentalmanagement.app.utilities.DashboardMapper;
import java.util.Collections;
import java.util.List;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
public class AdminDashboardActivity extends DashboardScreenActivity {

    private DashboardRepository dashboardRepository;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityDashboardBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());
        dashboardRepository = new DashboardRepository();
        bindCommonHeader(displayName(), getString(R.string.admin_dashboard));
        binding.cardCurrentUnit.setVisibility(View.GONE);
        binding.tvStatsTitle.setText("Overview");
        binding.tvStatsTitle.setVisibility(View.VISIBLE);
        binding.rvStats.setVisibility(View.VISIBLE);
        binding.tvActionsTitle.setText("Admin Actions");
        binding.tvActionsTitle.setVisibility(View.VISIBLE);
        binding.rvActions.setVisibility(View.VISIBLE);
        binding.tvPrimaryTitle.setText("Recent Registrations");
        binding.tvPrimaryTitle.setVisibility(View.GONE);
        binding.rvPrimary.setVisibility(View.GONE);
        binding.tvSecondaryTitle.setText("Recent Payments");
        binding.tvSecondaryTitle.setVisibility(View.GONE);
        binding.rvSecondary.setVisibility(View.GONE);
        setupGridRecycler(binding.rvStats, 1);
        setupGridRecycler(binding.rvActions, 2);
        setupListRecycler(binding.rvPrimary);
        setupListRecycler(binding.rvSecondary);
        binding.swipeRefreshLayout.setOnRefreshListener(this::loadDashboard);
        binding.btnRetry.setOnClickListener(v -> loadDashboard());
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
        dashboardRepository.adminDashboard(authorization).enqueue(new Callback<AdminDashboardResponse>() {
            @Override
            public void onResponse(Call<AdminDashboardResponse> call, Response<AdminDashboardResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    showError("Failed to load dashboard. Please try again.", v -> loadDashboard());
                    return;
                }
                render(response.body());
            }

            @Override
            public void onFailure(Call<AdminDashboardResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load dashboard. Please try again.", v -> loadDashboard());
            }
        });
    }

    private void render(AdminDashboardResponse response) {
        hideError();
        binding.tvStatsTitle.setVisibility(View.VISIBLE);
        binding.rvStats.setVisibility(View.VISIBLE);
        binding.rvStats.setAdapter(new DashboardMetricAdapter(DashboardMapper.adminMetrics(response.getStats())));
        binding.tvActionsTitle.setVisibility(View.VISIBLE);
        binding.rvActions.setVisibility(View.VISIBLE);
        binding.rvActions.setAdapter(new DashboardActionAdapter(DashboardMapper.adminActions(), item -> {
            if ("Manage Landlords".equalsIgnoreCase(item.getTitle())) {
                openScreen(AdminLandlordsActivity.class);
            }
        }));
        renderListSection(binding.tvPrimaryTitle, binding.rvPrimary, "Recent Registrations", DashboardMapper.recentRegistrations(safeList(response.getRecentRegistrations())));
        renderListSection(binding.tvSecondaryTitle, binding.rvSecondary, "Recent Payments", DashboardMapper.recentPayments(safeList(response.getRecentPayments())));
    }

    private void openScreen(Class<?> targetClass) {
        Intent intent = new Intent(this, targetClass);
        intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
        startActivity(intent);
    }

    private <T> List<T> safeList(List<T> items) {
        return items == null ? Collections.emptyList() : items;
    }

    private void renderListSection(TextView titleView, RecyclerView recyclerView, String title, List<DashboardListItem> items) {
        if (items == null || items.isEmpty()) {
            titleView.setVisibility(View.GONE);
            recyclerView.setVisibility(View.GONE);
            recyclerView.setAdapter(null);
            return;
        }
        titleView.setText(title);
        titleView.setVisibility(View.VISIBLE);
        recyclerView.setVisibility(View.VISIBLE);
        recyclerView.setAdapter(new DashboardListAdapter(items));
    }
}
