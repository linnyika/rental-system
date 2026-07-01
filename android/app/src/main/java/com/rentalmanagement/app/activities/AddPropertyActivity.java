package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import com.rentalmanagement.app.databinding.ActivityAddPropertyBinding;
import com.rentalmanagement.app.models.AddPropertyRequest;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.repository.PropertyRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;

import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class AddPropertyActivity extends BaseDashboardActivity {

    private ActivityAddPropertyBinding binding;
    private PropertyRepository propertyRepository;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityAddPropertyBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        propertyRepository = new PropertyRepository();

        configureToolbar(binding.toolbar, "Add Property", true);
        binding.swipeRefreshLayout.setEnabled(false);
        binding.btnSaveProperty.setOnClickListener(v -> saveProperty());
        binding.btnRetry.setOnClickListener(v -> saveProperty());
    }

    private void saveProperty() {
        clearFieldErrors();

        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        String name = text(binding.etName);
        String address = text(binding.etAddress);

        if (name.isEmpty()) {
            binding.etName.setError("Property name is required");
            return;
        }

        setLoading(true);
        hideError();

        AddPropertyRequest request = new AddPropertyRequest(name, address.isEmpty() ? null : address);
        propertyRepository.addProperty(authorization, request).enqueue(new Callback<ApiMessageResponse>() {
            @Override
            public void onResponse(Call<ApiMessageResponse> call, Response<ApiMessageResponse> response) {
                setLoading(false);
                if (!response.isSuccessful()) {
                    ApiErrorParser.ParsedApiError parsed = ApiErrorParser.parse(
                            response,
                            "Failed to save property. Please verify details and try again."
                    );
                    applyFieldErrors(parsed.getFieldErrors());
                    showError(parsed.getMessage());
                    return;
                }

                String message = response.body() != null && response.body().getMessage() != null
                        ? response.body().getMessage()
                        : "Property created successfully";
                Toast.makeText(AddPropertyActivity.this, message, Toast.LENGTH_SHORT).show();
                finish();
            }

            @Override
            public void onFailure(Call<ApiMessageResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to save property. Please try again.");
            }
        });
    }

    private void clearFieldErrors() {
        binding.etName.setError(null);
        binding.etAddress.setError(null);
    }

    private void applyFieldErrors(Map<String, String> fieldErrors) {
        if (fieldErrors == null || fieldErrors.isEmpty()) {
            return;
        }

        if (fieldErrors.containsKey("name")) {
            binding.etName.setError(fieldErrors.get("name"));
        }
        if (fieldErrors.containsKey("address")) {
            binding.etAddress.setError(fieldErrors.get("address"));
        }
    }

    private String text(com.google.android.material.textfield.TextInputEditText input) {
        return input.getText() == null ? "" : input.getText().toString().trim();
    }

    private String authorizationHeader() {
        String token = sessionManager.getToken();
        return token == null || token.trim().isEmpty() ? null : "Bearer " + token;
    }

    private void setLoading(boolean loading) {
        binding.progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
    }

    private void showError(String message) {
        binding.cardError.setVisibility(View.VISIBLE);
        binding.tvErrorMessage.setText(message);
    }

    private void hideError() {
        binding.cardError.setVisibility(View.GONE);
    }
}
