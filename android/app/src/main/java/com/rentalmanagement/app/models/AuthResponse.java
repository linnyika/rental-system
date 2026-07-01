package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class AuthResponse {

    @SerializedName("access_token")
    private String accessToken;

    @SerializedName("token_type")
    private String tokenType;

    @SerializedName("user")
    private User user;

    @SerializedName("role")
    private String role;

    public String getAccessToken() {
        return accessToken;
    }

    public String getTokenType() {
        return tokenType;
    }

    public User getUser() {
        return user;
    }

    public String getRole() {
        return role;
    }
}
