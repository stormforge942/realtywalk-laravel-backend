export * from "./eventbus";
export * from "./env";

export const deepClone = (obj) => {
    return JSON.parse(JSON.stringify(obj));
};
