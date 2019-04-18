import { IRoutes } from "../core/routes/interfaces/IRoutes";
import authorization from "./authorization";
import calculation from "./calculation";
import participant from "./participant";
import user from "./user";

export const routes: IRoutes = {
	...participant,
	...authorization,
	...calculation,
	...user
};
