package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.LinearLayoutManager;

import com.rentalmanagement.app.adapters.DashboardListAdapter;
import com.rentalmanagement.app.databinding.ActivityAdminLandlordsBinding;
import com.rentalmanagement.app.databinding.DialogEditLandlordBinding;
import com.rentalmanagement.app.models.AdminLandlordItem;
import com.rentalmanagement.app.models.AdminLandlordsResponse;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.DashboardListItem;
import com.rentalmanagement.app.models.UpdateLandlordRequest;
import com.rentalmanagement.app.repository.AdminRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class AdminLandlordsActivity extends BaseDashboardActivity {

    private ActivityAdminLandlordsBinding binding;
    private AdminRepository adminRepository;
    private final List<AdminLandlordItem> landlords = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityAdminLandlordsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        adminRepository = new AdminRepository();

        configureToolbar(binding.toolbar, "Manage Landlords", true);
        binding.rvLandlords.setLayoutManager(new LinearLayoutManager(this));
        binding.rvLandlords.setNestedScrollingEnabled(false);

        binding.swipeRefreshLayout.setOnRefreshListener(this::loadLandlords);
        binding.btnRetry.setOnClickListener(v -> loadLandlords());

        loadLandlords();
    }

    private void loadLandlords() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        adminRepository.landlords(authorization).enqueue(new Callback<AdminLandlordsResponse>() {
            @Override
            public void onResponse(Call<AdminLandlordsResponse> call, Response<AdminLandlordsResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to load landlords.");
                    showError(parsedError.getMessage());
                    return;
                }

                landlords.clear();
                if (response.body().getLandlords() != null) {
                    landlords.addAll(response.body().getLandlords());
                }
                renderLandlords();
            }

            @Override
            public void onFailure(Call<AdminLandlordsResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load landlords. Please try again.");
            }
        });
    }

    private void renderLandlords() {
        hideError();
        if (landlords.isEmpty()) {
            binding.tvEmpty.setVisibility(View.VISIBLE);
            binding.rvLandlords.setVisibility(View.GONE);
            binding.rvLandlords.setAdapter(new DashboardListAdapter(Collections.emptyList()));
            return;
        }

        List<DashboardListItem> items = new ArrayList<>();
        for (AdminLandlordItem landlord : landlords) {
            if (landlord == null) {
                continue;
            }
            String contact = landlord.getEmail() == null || landlord.getEmail().trim().isEmpty()
                    ? safe(landlord.getPhone())
                    : landlord.getEmail();

            int propertiesCount = landlord.getPropertiesCount() == null ? 0 : landlord.getPropertiesCount();

            items.add(new DashboardListItem(
                    safe(landlord.getName()),
                    contact,
                    "Landlord",
                    "Properties: " + propertiesCount + " - " + safe(landlord.getCreatedAt())
            ));
        }

        binding.tvEmpty.setVisibility(items.isEmpty() ? View.VISIBLE : View.GONE);
        binding.rvLandlords.setVisibility(items.isEmpty() ? View.GONE : View.VISIBLE);
        binding.rvLandlords.setAdapter(new DashboardListAdapter(items, (item, position) -> {
            if (position < 0 || position >= landlords.size()) {
                return;
            }
            showManageDialog(landlords.get(position));
        }));
    }

    private void showManageDialog(AdminLandlordItem landlord) {
        new AlertDialog.Builder(this)
                .setTitle(safe(landlord.getName()))
                .setPositiveButton("Edit", (dialog, which) -> showEditDialog(landlord))
                .setNeutralButton("Delete", (dialog, which) -> confirmDelete(landlord))
                .setNegativeButton("Cancel", null)
                .show();
    }

    private void showEditDialog(AdminLandlordItem landlord) {
        DialogEditLandlordBinding dialogBinding = DialogEditLandlordBinding.inflate(getLayoutInflater());
        dialogBinding.etName.setText(safeForEdit(landlord.getName()));
        dialogBinding.etEmail.setText(safeForEdit(landlord.getEmail()));
        dialogBinding.etPhone.setText(safeForEdit(landlord.getPhone()));

        AlertDialog dialog = new AlertDialog.Builder(this)
                .setTitle("Update landlord")
                .setView(dialogBinding.getRoot())
                .setPositiveButton("Save", null)
                .setNegativeButton("Cancel", null)
                .create();
        dialog.show();

        dialog.getButton(AlertDialog.BUTTON_POSITIVE).setOnClickListener(v -> {
            dialogBinding.etName.setError(null);
            dialogBinding.etEmail.setError(null);

            String name = text(dialogBinding.etName);
            String email = text(dialogBinding.etEmail);
            String phone = text(dialogBinding.etPhone);

            if (name.isEmpty()) {
                dialogBinding.etName.setError("Name is required");
                return;
            }
            if (email.isEmpty()) {
                dialogBinding.etEmail.setError("Email is required");
                return;
            }

            updateLandlord(landlord.getId(), name, email, phone.isEmpty() ? null : phone, dialog, dialogBinding.etName, dialogBinding.etEmail);
        });
    }

    private void updateLandlord(Long landlordId, String name, String email, String phone, AlertDialog dialog, com.google.android.material.textfield.TextInputEditText etName, com.google.android.material.textfield.TextInputEditText etEmail) {
        if (landlordId == null || landlordId <= 0) {
            Toast.makeText(this, "Invalid landlord selected.", Toast.LENGTH_SHORT).show();
            return;
        }

        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        adminRepository.updateLandlord(authorization, landlordId, new UpdateLandlordRequest(name, email, phone))
                .enqueue(new Callback<ApiMessageResponse>() {
                    @Override
                    public void onResponse(Call<ApiMessageResponse> call, Response<ApiMessageResponse> response) {
                        setLoading(false);
                        if (!response.isSuccessful()) {
                            ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to update landlord.");
                            if (parsedError.getFieldErrors().containsKey("name")) {
                                etName.setError(parsedError.getFieldErrors().get("name"));
                            }
                            if (parsedError.getFieldErrors().containsKey("email")) {
                                etEmail.setError(parsedError.getFieldErrors().get("email"));
                            }
                            showError(parsedError.getMessage());
                            return;
                        }

                        Toast.makeText(AdminLandlordsActivity.this, "Landlord updated", Toast.LENGTH_SHORT).show();
                        dialog.dismiss();
                        loadLandlords();
                    }

                    @Override
                    public void onFailure(Call<ApiMessageResponse> call, Throwable t) {
                        setLoading(false);
                        showError("Unable to update landlord. Please try again.");
                    }
                });
    }

    private void confirmDelete(AdminLandlordItem landlord) {
        if (landlord.getId() == null || landlord.getId() <= 0) {
            Toast.makeText(this, "Invalid landlord selected.", Toast.LENGTH_SHORT).show();
            return;
        }

        new AlertDialog.Builder(this)
                .setTitle("Delete landlord")
                .setMessage("Delete this landlord and related properties, units, tenants, caretakers, and records?")
                .setPositiveButton("Delete", (dialog, which) -> deleteLandlord(landlord.getId()))
                .setNegativeButton("Cancel", null)
                .show();
    }

    private void deleteLandlord(long landlordId) {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        adminRepository.deleteLandlord(authorization, landlordId).enqueue(new Callback<ApiMessageResponse>() {
            @Override
            public void onResponse(Call<ApiMessageResponse> call, Response<ApiMessageResponse> response) {
                setLoading(false);
                if (!response.isSuccessful()) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to delete landlord.");
                    showError(parsedError.getMessage());
                    return;
                }

                Toast.makeText(AdminLandlordsActivity.this, "Landlord deleted", Toast.LENGTH_SHORT).show();
                loadLandlords();
            }

            @Override
            public void onFailure(Call<ApiMessageResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to delete landlord. Please try again.");
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

    private String safe(String value) {
        return value == null || value.trim().isEmpty() ? "-" : value;
    }

    private String safeForEdit(String value) {
        return value == null || value.trim().isEmpty() ? "" : value;
    }

    private String text(com.google.android.material.textfield.TextInputEditText input) {
        return input.getText() == null ? "" : input.getText().toString().trim();
    }

}
