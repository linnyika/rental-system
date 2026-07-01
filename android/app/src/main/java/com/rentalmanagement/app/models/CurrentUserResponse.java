package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class CurrentUserResponse {

    @SerializedName("user")
    private User user;

    public User getUser() {
        return user;
    }
}
