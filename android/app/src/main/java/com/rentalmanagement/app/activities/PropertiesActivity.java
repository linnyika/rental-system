package com.rentalmanagement.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.rentalmanagement.app.adapters.DashboardListAdapter;
import com.rentalmanagement.app.databinding.ActivityPropertiesBinding;
import com.rentalmanagement.app.models.PropertiesResponse;
import com.rentalmanagement.app.models.PropertyItem;
import com.rentalmanagement.app.repository.PropertyRepository;
import com.rentalmanagement.app.utilities.DashboardMapper;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class PropertiesActivity extends BaseDashboardActivity {

    public static final String EXTRA_PROPERTY_ID = "extra_property_id";
    public static final String EXTRA_PROPERTY_NAME = "extra_property_name";
    public static final String EXTRA_PROPERTY_ADDRESS = "extra_property_address";

    private ActivityPropertiesBinding binding;
    private PropertyRepository propertyRepository;
    private final List<PropertyItem> properties = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityPropertiesBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        propertyRepository = new PropertyRepository();

        configureToolbar(binding.toolbar, "Properties", true);

        binding.rvProperties.setLayoutManager(new LinearLayoutManager(this));
        binding.rvProperties.setNestedScrollingEnabled(false);
        binding.swipeRefreshLayout.setOnRefreshListener(this::loadProperties);
        binding.btnRetry.setOnClickListener(v -> loadProperties());
        binding.fabAddProperty.setOnClickListener(v -> {
            Intent intent = new Intent(this, AddPropertyActivity.class);
            intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
            startActivity(intent);
        });

        loadProperties();
    }

    @Override
    protected void onResume() {
        super.onResume();
        loadProperties();
    }

    private void loadProperties() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        propertyRepository.properties(authorization).enqueue(new Callback<PropertiesResponse>() {
            @Override
            public void onResponse(Call<PropertiesResponse> call, Response<PropertiesResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    showError("Failed to load properties. Please try again.");
                    return;
                }

                properties.clear();
                if (response.body().getProperties() != null) {
                    properties.addAll(response.body().getProperties());
                }
                renderProperties();
            }

            @Override
            public void onFailure(Call<PropertiesResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load properties. Please try again.");
            }
        });
    }

    private void renderProperties() {
        hideError();
        boolean isEmpty = properties.isEmpty();
        binding.tvEmpty.setVisibility(isEmpty ? View.VISIBLE : View.GONE);
        binding.rvProperties.setVisibility(isEmpty ? View.GONE : View.VISIBLE);

        if (isEmpty) {
            binding.rvProperties.setAdapter(new DashboardListAdapter(Collections.emptyList()));
            return;
        }

        binding.rvProperties.setAdapter(new DashboardListAdapter(
                DashboardMapper.properties(properties),
                (item, position) -> {
                    if (position < 0 || position >= properties.size()) {
                        return;
                    }
                    PropertyItem property = properties.get(position);
                    Intent intent = new Intent(PropertiesActivity.this, PropertyDetailsActivity.class);
                    intent.putExtra(EXTRA_PROPERTY_ID, property.getId() == null ? -1 : property.getId());
                    intent.putExtra(EXTRA_PROPERTY_NAME, property.getName());
                    intent.putExtra(EXTRA_PROPERTY_ADDRESS, property.getAddress());
                    startActivity(intent);
                }
        ));
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
