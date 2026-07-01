package com.rentalmanagement.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.rentalmanagement.app.adapters.DashboardListAdapter;
import com.rentalmanagement.app.databinding.ActivityPropertyDetailsBinding;
import com.rentalmanagement.app.models.UnitItem;
import com.rentalmanagement.app.models.UnitsResponse;
import com.rentalmanagement.app.repository.UnitRepository;
import com.rentalmanagement.app.utilities.DashboardMapper;

import java.util.Collections;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class PropertyDetailsActivity extends BaseDashboardActivity {

    private ActivityPropertyDetailsBinding binding;
    private UnitRepository unitRepository;

    private int propertyId;
    private String propertyName;
    private String propertyAddress;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityPropertyDetailsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        unitRepository = new UnitRepository();

        propertyId = getIntent().getIntExtra(PropertiesActivity.EXTRA_PROPERTY_ID, -1);
        propertyName = getIntent().getStringExtra(PropertiesActivity.EXTRA_PROPERTY_NAME);
        propertyAddress = getIntent().getStringExtra(PropertiesActivity.EXTRA_PROPERTY_ADDRESS);

        configureToolbar(binding.toolbar, "Property Details", true);

        binding.rvUnits.setLayoutManager(new LinearLayoutManager(this));
        binding.rvUnits.setNestedScrollingEnabled(false);
        binding.swipeRefreshLayout.setOnRefreshListener(this::loadUnits);
        binding.btnRetry.setOnClickListener(v -> loadUnits());

        binding.fabAddUnit.setOnClickListener(v -> {
            Intent intent = new Intent(this, UnitsActivity.class);
            intent.putExtra(UnitsActivity.EXTRA_PROPERTY_ID, propertyId);
            startActivity(intent);
        });

        bindPropertyHeader();
        loadUnits();
    }

    private void bindPropertyHeader() {
        binding.tvPropertyName.setText(safe(propertyName));
        binding.tvLocation.setText("Location: " + safe(propertyAddress));
        binding.tvDescription.setText("Description: Not available");
        binding.tvUnitsSummary.setText("Number of units: 0");
        binding.tvOccupancySummary.setText("Occupancy: Not available");
    }

    private void loadUnits() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        if (propertyId <= 0) {
            showError("Invalid property. Please open the property again.");
            return;
        }

        setLoading(true);
        hideError();

        unitRepository.units(authorization, propertyId).enqueue(new Callback<UnitsResponse>() {
            @Override
            public void onResponse(Call<UnitsResponse> call, Response<UnitsResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    showError("Failed to load property units. Please try again.");
                    return;
                }

                List<UnitItem> units = response.body().getUnits() == null
                        ? Collections.emptyList()
                        : response.body().getUnits();
                renderUnits(units);
            }

            @Override
            public void onFailure(Call<UnitsResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load property units. Please try again.");
            }
        });
    }

    private void renderUnits(List<UnitItem> units) {
        hideError();
        boolean isEmpty = units == null || units.isEmpty();
        binding.tvEmpty.setVisibility(isEmpty ? View.VISIBLE : View.GONE);
        binding.rvUnits.setVisibility(isEmpty ? View.GONE : View.VISIBLE);

        if (isEmpty) {
            binding.rvUnits.setAdapter(new DashboardListAdapter(Collections.emptyList()));
            binding.tvUnitsSummary.setText("Number of units: 0");
            binding.tvOccupancySummary.setText("Occupancy: 0 occupied, 0 available");
            return;
        }

        int occupied = 0;
        for (UnitItem unit : units) {
            if (unit.getOccupied() != null && unit.getOccupied()) {
                occupied++;
            }
        }
        int total = units.size();
        int available = Math.max(total - occupied, 0);

        binding.tvUnitsSummary.setText("Number of units: " + total);
        binding.tvOccupancySummary.setText("Occupancy: " + occupied + " occupied, " + available + " available");
        binding.rvUnits.setAdapter(new DashboardListAdapter(DashboardMapper.units(units)));
    }

    private String safe(String value) {
        return value == null || value.trim().isEmpty() ? "-" : value;
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
