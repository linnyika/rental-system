package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.rentalmanagement.app.adapters.MaintenanceAdapter;
import com.rentalmanagement.app.adapters.TaskAdapter;
import com.rentalmanagement.app.databinding.ActivityTenantMaintenanceBinding;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.MaintenanceActionResponse;
import com.rentalmanagement.app.models.MaintenanceCreateRequest;
import com.rentalmanagement.app.models.MaintenanceRequestItem;
import com.rentalmanagement.app.models.MaintenanceRequestsResponse;
import com.rentalmanagement.app.models.TaskItem;
import com.rentalmanagement.app.models.TasksResponse;
import com.rentalmanagement.app.repository.MaintenanceRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class TenantMaintenanceActivity extends BaseDashboardActivity {

    private ActivityTenantMaintenanceBinding binding;
    private MaintenanceRepository maintenanceRepository;
    private final List<MaintenanceRequestItem> requests = new ArrayList<>();
    private final List<TaskItem> completedTasks = new ArrayList<>();
    private int pendingListCalls = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityTenantMaintenanceBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        maintenanceRepository = new MaintenanceRepository();

        configureToolbar(binding.toolbar, "Maintenance", true);

        binding.rvRequests.setLayoutManager(new LinearLayoutManager(this));
        binding.rvRequests.setNestedScrollingEnabled(false);
        binding.rvConfirmTasks.setLayoutManager(new LinearLayoutManager(this));
        binding.rvConfirmTasks.setNestedScrollingEnabled(false);

        binding.btnSubmit.setOnClickListener(v -> submitRequest());
        binding.swipeRefreshLayout.setOnRefreshListener(this::loadAll);
        binding.btnRetry.setOnClickListener(v -> loadAll());

        loadAll();
    }

    private void submitRequest() {
        clearInputErrors();

        String description = text();
        if (description.isEmpty()) {
            binding.etDescription.setError("Description is required");
            return;
        }

        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        maintenanceRepository.submitMaintenanceRequest(
                authorization,
                new MaintenanceCreateRequest(description, binding.switchMajor.isChecked())
        ).enqueue(new Callback<MaintenanceActionResponse>() {
            @Override
            public void onResponse(Call<MaintenanceActionResponse> call, Response<MaintenanceActionResponse> response) {
                setLoading(false);
                if (!response.isSuccessful()) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to submit maintenance request.");
                    showError(parsedError.getMessage());
                    if (parsedError.getFieldErrors().containsKey("description")) {
                        binding.etDescription.setError(parsedError.getFieldErrors().get("description"));
                    }
                    return;
                }

                binding.etDescription.setText("");
                binding.switchMajor.setChecked(false);
                Toast.makeText(TenantMaintenanceActivity.this, "Maintenance request submitted", Toast.LENGTH_SHORT).show();
                loadAll();
            }

            @Override
            public void onFailure(Call<MaintenanceActionResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to submit maintenance request. Please try again.");
            }
        });
    }

    private void loadAll() {
        pendingListCalls = 2;
        setLoading(true);
        hideError();
        loadRequests();
        loadCompletedTasks();
    }

    private void loadRequests() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        maintenanceRepository.tenantMaintenanceRequests(authorization).enqueue(new Callback<MaintenanceRequestsResponse>() {
            @Override
            public void onResponse(Call<MaintenanceRequestsResponse> call, Response<MaintenanceRequestsResponse> response) {
                finishListCall();
                if (!response.isSuccessful() || response.body() == null) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to load maintenance requests.");
                    showError(parsedError.getMessage());
                    binding.tvRequestsEmpty.setVisibility(View.VISIBLE);
                    binding.rvRequests.setVisibility(View.GONE);
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
                finishListCall();
                showError("Unable to load maintenance requests. Please try again.");
                binding.tvRequestsEmpty.setVisibility(View.VISIBLE);
                binding.rvRequests.setVisibility(View.GONE);
            }
        });
    }

    private void loadCompletedTasks() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        maintenanceRepository.tenantCompletedTasks(authorization).enqueue(new Callback<TasksResponse>() {
            @Override
            public void onResponse(Call<TasksResponse> call, Response<TasksResponse> response) {
                finishListCall();
                if (!response.isSuccessful() || response.body() == null) {
                    binding.tvConfirmEmpty.setText("Unable to load completed tasks awaiting confirmation.");
                    binding.tvConfirmEmpty.setVisibility(View.VISIBLE);
                    binding.rvConfirmTasks.setVisibility(View.GONE);
                    return;
                }

                completedTasks.clear();
                if (response.body().getTasks() != null) {
                    completedTasks.addAll(response.body().getTasks());
                }
                renderConfirmTasks();
            }

            @Override
            public void onFailure(Call<TasksResponse> call, Throwable t) {
                finishListCall();
                binding.tvConfirmEmpty.setText("Unable to load completed tasks awaiting confirmation.");
                binding.tvConfirmEmpty.setVisibility(View.VISIBLE);
                binding.rvConfirmTasks.setVisibility(View.GONE);
            }
        });
    }

    private void renderRequests() {
        if (requests.isEmpty()) {
            binding.tvRequestsEmpty.setVisibility(View.VISIBLE);
            binding.rvRequests.setVisibility(View.GONE);
            binding.rvRequests.setAdapter(new MaintenanceAdapter(new ArrayList<>(), false, null));
            return;
        }

        binding.tvRequestsEmpty.setVisibility(View.GONE);
        binding.rvRequests.setVisibility(View.VISIBLE);
        binding.rvRequests.setAdapter(new MaintenanceAdapter(requests, false, null));
    }

    private void renderConfirmTasks() {
        if (completedTasks.isEmpty()) {
            binding.tvConfirmEmpty.setText("No completed tasks awaiting confirmation.");
            binding.tvConfirmEmpty.setVisibility(View.VISIBLE);
            binding.rvConfirmTasks.setVisibility(View.GONE);
            binding.rvConfirmTasks.setAdapter(new TaskAdapter(new ArrayList<>(), TaskAdapter.Mode.TENANT_CONFIRM, null));
            return;
        }

        binding.tvConfirmEmpty.setVisibility(View.GONE);
        binding.rvConfirmTasks.setVisibility(View.VISIBLE);
        binding.rvConfirmTasks.setAdapter(new TaskAdapter(completedTasks, TaskAdapter.Mode.TENANT_CONFIRM, (item, action) -> {
            if (action == TaskAdapter.TaskAction.CONFIRM) {
                confirmCompletion(item.getId());
            }
        }));
    }

    private void confirmCompletion(long taskId) {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        maintenanceRepository.confirmTaskCompletion(authorization, taskId).enqueue(new Callback<ApiMessageResponse>() {
            @Override
            public void onResponse(Call<ApiMessageResponse> call, Response<ApiMessageResponse> response) {
                setLoading(false);
                if (!response.isSuccessful()) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to confirm task completion.");
                    showError(parsedError.getMessage());
                    return;
                }
                Toast.makeText(TenantMaintenanceActivity.this, "Maintenance confirmed", Toast.LENGTH_SHORT).show();
                loadAll();
            }

            @Override
            public void onFailure(Call<ApiMessageResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to confirm completion. Please try again.");
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

    private String text() {
        return binding.etDescription.getText() == null ? "" : binding.etDescription.getText().toString().trim();
    }

    private void clearInputErrors() {
        binding.etDescription.setError(null);
    }

    private void finishListCall() {
        pendingListCalls = Math.max(0, pendingListCalls - 1);
        if (pendingListCalls == 0) {
            setLoading(false);
        }
    }

}
