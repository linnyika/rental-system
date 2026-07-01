package com.rentalmanagement.app.activities;

import android.view.View;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import com.rentalmanagement.app.databinding.ActivityDashboardBinding;
import com.rentalmanagement.app.R;
import java.util.Locale;
public abstract class DashboardScreenActivity extends BaseDashboardActivity {

    protected ActivityDashboardBinding binding;

    protected void bindCommonHeader(String userName, String roleLabel) {
        binding.tvUserName.setText(userName);
        binding.tvUserRole.setText(roleLabel);
        binding.tvProfilePlaceholder.setText(initialFor(userName));
        binding.btnLogout.setOnClickListener(v -> logout());
    }

    protected void setupGridRecycler(androidx.recyclerview.widget.RecyclerView recyclerView, int spanCount) {
        recyclerView.setLayoutManager(new GridLayoutManager(this, spanCount));
        recyclerView.setNestedScrollingEnabled(false);
    }

    protected void setupListRecycler(androidx.recyclerview.widget.RecyclerView recyclerView) {
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        recyclerView.setNestedScrollingEnabled(false);
    }

    protected void setLoading(boolean loading) {
        binding.progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
        binding.swipeRefreshLayout.setRefreshing(loading);
    }

    protected void showError(String message, View.OnClickListener retryListener) {
        binding.cardError.setVisibility(View.VISIBLE);
        binding.tvErrorMessage.setText(message);
        binding.btnRetry.setOnClickListener(retryListener);
    }

    protected void hideError() {
        binding.cardError.setVisibility(View.GONE);
    }

    protected String authorizationHeader() {
        String token = sessionManager.getToken();
        return token == null || token.trim().isEmpty() ? null : "Bearer " + token;
    }

    protected String displayName() {
        String userName = sessionManager.getUserName();
        return userName == null || userName.trim().isEmpty() ? getString(R.string.app_name) : userName;
    }

    private String initialFor(String value) {
        if (value == null || value.trim().isEmpty()) {
            return "U";
        }
        return value.trim().substring(0, 1).toUpperCase(Locale.ROOT);
    }
}
