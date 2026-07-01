package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.view.View;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.rentalmanagement.app.adapters.ActivityLogAdapter;
import com.rentalmanagement.app.databinding.ActivityCaretakerActivityLogsBinding;
import com.rentalmanagement.app.models.ActivityLogItem;
import com.rentalmanagement.app.models.ActivityLogsResponse;
import com.rentalmanagement.app.repository.MaintenanceRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;

import java.util.Collections;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class CaretakerActivityLogsActivity extends BaseDashboardActivity {

    private ActivityCaretakerActivityLogsBinding binding;
    private MaintenanceRepository maintenanceRepository;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityCaretakerActivityLogsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        maintenanceRepository = new MaintenanceRepository();

        configureToolbar(binding.toolbar, "Activity Logs", true);

        binding.rvLogs.setLayoutManager(new LinearLayoutManager(this));
        binding.rvLogs.setNestedScrollingEnabled(false);

        binding.swipeRefreshLayout.setOnRefreshListener(this::loadLogs);
        binding.btnRetry.setOnClickListener(v -> loadLogs());

        loadLogs();
    }

    private void loadLogs() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        maintenanceRepository.caretakerActivityLogs(authorization).enqueue(new Callback<ActivityLogsResponse>() {
            @Override
            public void onResponse(Call<ActivityLogsResponse> call, Response<ActivityLogsResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to load activity logs.");
                    showError(parsedError.getMessage());
                    return;
                }

                List<ActivityLogItem> logs = response.body().getLogs() == null
                        ? Collections.emptyList()
                        : response.body().getLogs();
                render(logs);
            }

            @Override
            public void onFailure(Call<ActivityLogsResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load activity logs. Please try again.");
            }
        });
    }

    private void render(List<ActivityLogItem> logs) {
        if (logs.isEmpty()) {
            binding.tvEmpty.setVisibility(View.VISIBLE);
            binding.rvLogs.setVisibility(View.GONE);
            binding.rvLogs.setAdapter(new ActivityLogAdapter(Collections.emptyList()));
            return;
        }

        binding.tvEmpty.setVisibility(View.GONE);
        binding.rvLogs.setVisibility(View.VISIBLE);
        binding.rvLogs.setAdapter(new ActivityLogAdapter(logs));
    }

    private String authorizationHeader() {
        String token = sessionManager.getToken();
        return token == null || token.trim().isEmpty() ? null : "Bearer " + token;
    }

    private void setLoading(boolean loading) {
        binding.progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
        binding.swipeRefreshLayout.setRefreshing(loading);
    }

    private void showError(String message) {
        binding.cardError.setVisibility(View.VISIBLE);
        binding.tvErrorMessage.setText(message);
    }

    private void hideError() {
        binding.cardError.setVisibility(View.GONE);
    }

}
