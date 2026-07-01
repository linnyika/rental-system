package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.LinearLayoutManager;

import com.rentalmanagement.app.adapters.MaintenanceAdapter;
import com.rentalmanagement.app.databinding.ActivityLandlordMaintenanceBinding;
import com.rentalmanagement.app.models.MaintenanceActionResponse;
import com.rentalmanagement.app.models.MaintenanceRequestItem;
import com.rentalmanagement.app.models.MaintenanceRequestsResponse;
import com.rentalmanagement.app.repository.MaintenanceRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LandlordMaintenanceActivity extends BaseDashboardActivity {

    private ActivityLandlordMaintenanceBinding binding;
    private MaintenanceRepository maintenanceRepository;
    private final List<MaintenanceRequestItem> requests = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityLandlordMaintenanceBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        maintenanceRepository = new MaintenanceRepository();

        configureToolbar(binding.toolbar, "Maintenance Requests", true);
        binding.rvRequests.setLayoutManager(new LinearLayoutManager(this));
        binding.rvRequests.setNestedScrollingEnabled(false);

        binding.swipeRefreshLayout.setOnRefreshListener(this::loadRequests);
        binding.btnRetry.setOnClickListener(v -> loadRequests());

        loadRequests();
    }

    private void loadRequests() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        maintenanceRepository.maintenanceRequests(authorization).enqueue(new Callback<MaintenanceRequestsResponse>() {
            @Override
            public void onResponse(Call<MaintenanceRequestsResponse> call, Response<MaintenanceRequestsResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to load maintenance requests.");
                    showError(parsedError.getMessage());
                    return;
                }

                requests.clear();
                if (response.body().getRequests() != null) {
                    requests.addAll(response.body().getRequests());
                }
                renderRequests();
            }

            @Override
            public void onFailure(Call<MaintenanceRequestsResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load maintenance requests. Please try again.");
            }
        });
    }

    private void renderRequests() {
        hideError();
        if (requests.isEmpty()) {
            binding.tvEmpty.setVisibility(View.VISIBLE);
            binding.rvRequests.setVisibility(View.GONE);
            binding.rvRequests.setAdapter(new MaintenanceAdapter(new ArrayList<>(), true, null));
            return;
        }

        binding.tvEmpty.setVisibility(View.GONE);
        binding.rvRequests.setVisibility(View.VISIBLE);
        binding.rvRequests.setAdapter(new MaintenanceAdapter(requests, true, request -> {
            if (!"pending".equalsIgnoreCase(request.getStatus())) {
                return;
            }
            showDecisionDialog(request);
        }));
    }

    private void showDecisionDialog(MaintenanceRequestItem request) {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Update Request");
        builder.setMessage("Choose an action for this maintenance request.");
        builder.setPositiveButton("Approve", (dialog, which) -> updateStatus(request.getId(), "approved"));
        builder.setNegativeButton("Reject", (dialog, which) -> updateStatus(request.getId(), "rejected"));
        builder.setNeutralButton("Cancel", null);
        builder.show();
    }

    private void updateStatus(long requestId, String status) {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        maintenanceRepository.updateMaintenanceStatus(authorization, requestId, status).enqueue(new Callback<MaintenanceActionResponse>() {
            @Override
            public void onResponse(Call<MaintenanceActionResponse> call, Response<MaintenanceActionResponse> response) {
                setLoading(false);
                if (!response.isSuccessful()) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to update maintenance request.");
                    showError(parsedError.getMessage());
                    return;
                }
                Toast.makeText(LandlordMaintenanceActivity.this, "Request updated", Toast.LENGTH_SHORT).show();
                loadRequests();
            }

            @Override
            public void onFailure(Call<MaintenanceActionResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to update maintenance request. Please try again.");
            }
        });
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
